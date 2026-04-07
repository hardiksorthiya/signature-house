# Textile Management System

A comprehensive Enterprise Resource Planning (ERP) system designed specifically for textile manufacturing and machinery management. This Laravel-based application streamlines operations from lead management to machine installation and maintenance.

## 🚀 Features

### Core Modules

- **Dashboard** - Centralized overview with task management and analytics
- **Lead Management** - Track and manage business leads with geographic organization
- **Customer Management** - Comprehensive customer database and relationship management
- **Contract Management** - Digital contract creation, approval workflow, and signature management
- **Sales Management**
  - Proforma Invoice (PI) creation and management
  - Delivery tracking and status monitoring
  - Over invoice management
  - Machine status tracking
- **Payment Management** - Track payments, invoices, and financial transactions
- **Purchase Order Management** - Handle purchase orders and supplier relationships
- **Machine Unloading & Installation**
  - Pre-erection management
  - Image uploading and documentation
  - Damage tracking and reporting
  - Serial number management
  - Machine erection details
  - IA (Installation & Assembly) fitting tracking
- **Inventory Management** - Spare parts and inventory tracking
- **Machine Configuration** - Comprehensive machine specifications including:
  - Categories, Brands, Models, Sizes
  - Flange sizes, Feeders, Hooks
  - Nozzles, Dropins, Beams, Cloth Rollers
  - Software versions, HSN codes, WIR
  - Shafts, Levers, Chains, Heald Wires, E-Reads
- **Task Management** - Assign and track tasks with due dates and priorities
- **Reports** - Generate comprehensive business reports
- **Team Management** - User management with role-based access control (RBAC)
- **Settings** - System configuration and customization options

### Security & Access Control

- Role-based permissions (Super Admin, Admin, Manager, Staff)
- Granular permission system for fine-grained access control
- Secure authentication and authorization

## 🛠️ Technology Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **PDF Generation**: DomPDF
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL 5.7+ or MariaDB
- XAMPP (for local development) or similar environment

## 🔧 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/hardiksorthiya/textile.git
cd textile
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Update the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=textile
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup

**Option A: Using the provided script**
```bash
php create_database.php
```

**Option B: Manual creation**
1. Start MySQL service in XAMPP
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `textile`
4. Set collation to `utf8mb4_unicode_ci`

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed Database (Optional)

```bash
php artisan db:seed
```

This will create:
- Default roles and permissions
- Super Admin user (phone: 1234567890, password: password)
- Manager user (phone: 1234567891, password: password)
- Staff user (phone: 1234567892, password: password)

### 7. Build Frontend Assets

```bash
npm run build
```

Or for development with hot reload:

```bash
npm run dev
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 📁 Project Structure

```
textile/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/      # Application controllers
│   ├── Models/                # Eloquent models
│   └── Notifications/         # Email notifications
├── config/                     # Configuration files
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── public/                     # Public assets
├── resources/
│   ├── views/                 # Blade templates
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
├── routes/                     # Route definitions
└── storage/                     # Storage directory
```

## 🔐 Default Login Credentials

After seeding the database, you can login with:

- **Super Admin**
  - Phone: `1234567890`
  - Password: `password`

- **Manager**
  - Phone: `1234567891`
  - Password: `password`

- **Staff**
  - Phone: `1234567892`
  - Password: `password`

**⚠️ Important**: Change these default credentials in production!

## 📝 Additional Documentation

For detailed setup instructions, see [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)

## 🤝 Contributing

This is a private project. For contributions or issues, please contact the repository owner.

## 📄 License

This project is proprietary software. All rights reserved.

## 👥 Contact

For questions or support, please contact the development team.

---

**Repository**: [https://github.com/hardiksorthiya/textile](https://github.com/hardiksorthiya/textile)
