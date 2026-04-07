#!/bin/bash

# Laravel Deployment Script for Hostinger
# Run this script after uploading files to your server
#
# When run as root, Composer and Artisan run as DEPLOY_USER (default: signatureinhouse) so
# storage/framework/views and bootstrap/cache are not root-owned (avoids HTTP 500 on Blade recompile).

echo "🚀 Starting Laravel Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

DEPLOY_USER="${DEPLOY_USER:-signatureinhouse}"

run_as_deploy() {
  if [[ "$(id -u)" -eq 0 ]] && id "$DEPLOY_USER" &>/dev/null; then
    sudo -u "$DEPLOY_USER" -- "$@"
  else
    "$@"
  fi
}

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  .env file not found. Creating from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✅ .env file created. Please edit it with your production settings.${NC}"
    echo -e "${RED}❌ Please configure .env file before continuing!${NC}"
    exit 1
fi

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}⚠️  APP_KEY not set. Generating...${NC}"
    run_as_deploy php artisan key:generate
    echo -e "${GREEN}✅ APP_KEY generated${NC}"
fi

# Install Composer dependencies
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
run_as_deploy composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
else
    echo -e "${RED}❌ Composer installation failed${NC}"
    exit 1
fi

# Install NPM dependencies and build
if command -v npm &> /dev/null; then
    echo -e "${YELLOW}📦 Installing NPM dependencies...${NC}"
    npm install --production
    echo -e "${YELLOW}🔨 Building production assets...${NC}"
    npm run build
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Assets built successfully${NC}"
    else
        echo -e "${RED}❌ Asset build failed${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  NPM not found. Skipping asset build.${NC}"
    echo -e "${YELLOW}   Make sure to build assets manually or upload public/build folder${NC}"
fi

# Set ownership + permissions (required if anything above ran as root without run_as_deploy)
echo -e "${YELLOW}🔐 Setting file permissions...${NC}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [[ "$(id -u)" -eq 0 ]]; then
    bash "$SCRIPT_DIR/scripts/fix-storage-permissions.sh"
else
    chmod -R 775 storage bootstrap/cache
fi
echo -e "${GREEN}✅ Permissions set${NC}"

# Create storage link
echo -e "${YELLOW}🔗 Creating storage link...${NC}"
run_as_deploy php artisan storage:link
echo -e "${GREEN}✅ Storage link created${NC}"

# Clear all caches
echo -e "${YELLOW}🧹 Clearing caches...${NC}"
run_as_deploy php artisan config:clear
run_as_deploy php artisan cache:clear
run_as_deploy php artisan route:clear
run_as_deploy php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"

# Run migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
read -p "Do you want to run migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    run_as_deploy php artisan migrate --force
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Migrations completed${NC}"
    else
        echo -e "${RED}❌ Migration failed. Please check your database configuration.${NC}"
    fi
fi

# Cache configuration for production
echo -e "${YELLOW}⚡ Caching configuration for production...${NC}"
run_as_deploy php artisan config:cache
run_as_deploy php artisan route:cache
run_as_deploy php artisan view:cache
echo -e "${GREEN}✅ Configuration cached${NC}"

echo ""
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo ""
echo "Next steps:"
echo "1. Verify your .env file has correct production settings"
echo "2. Test your website at https://textile.findmeout.net"
echo "3. Change default passwords"
echo "4. Set up backups"
echo ""
