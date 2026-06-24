#!/usr/bin/env bash
# Start Expo dev server for Expo Go (scan QR in terminal).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

node_ok() {
  node -e "
    const [major, minor, patch] = process.versions.node.split('.').map(Number);
    process.exit(
      major > 20 || (major === 20 && (minor > 19 || (minor === 19 && patch >= 4))) ? 0 : 1
    );
  " 2>/dev/null
}

use_nvm() {
  export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
  if [ ! -s "$NVM_DIR/nvm.sh" ]; then
    return 1
  fi
  unset npm_config_prefix
  # shellcheck disable=SC1091
  . "$NVM_DIR/nvm.sh"
  if [ -f .nvmrc ]; then
    nvm install
    nvm use
  fi
}

if ! node_ok; then
  echo "Node $(node -v 2>/dev/null || echo 'not found') is too old (need >= 20.19.4)."
  if use_nvm && node_ok; then
    echo "Switched to Node $(node -v) via nvm."
  else
    echo "Install Node 20: curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt install -y nodejs"
    exit 1
  fi
else
  echo "Using Node $(node -v)"
fi

export EXPO_NO_REACT_NATIVE_DEVTOOLS="${EXPO_NO_REACT_NATIVE_DEVTOOLS:-1}"

if [ -f .env ]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

EXPO_USE_TUNNEL="${EXPO_USE_TUNNEL:-1}"
EXPO_PROJECT_URL="${EXPO_PROJECT_URL:-https://expo.dev/accounts/signaturetmservice/projects/signature-in-house}"

echo ""
echo "=============================================="
echo "  Expo Go — Signature In House"
echo "=============================================="
echo ""
echo "IMPORTANT: This app runs on a REMOTE server (cloud VPS)."
echo "Your phone WiFi is NOT the same network as this server."
echo "LAN QR (exp://82.x.x.x:8081) will NOT work from your phone."
echo ""
echo "Best option (no npm start needed):"
echo "  1. Install Expo Go on phone"
echo "  2. Log in as: signaturetmservice (same Expo account)"
echo "  3. Open: $EXPO_PROJECT_URL"
echo "  4. Tap \"Open in Expo Go\""
echo ""
echo "Or run: npm run publish:preview  (already published updates work in Expo Go)"
echo ""
if [[ " $* " == *" --lan "* ]] || [[ " $* " == *" --localhost "* ]]; then
  echo "Dev server: LAN mode (phone must be on same WiFi as THIS machine)"
  echo "=============================================="
  echo ""
  exec npx expo start "$@"
fi

if [[ " $* " == *" --tunnel "* ]] || [ "$EXPO_USE_TUNNEL" = "1" ]; then
  echo "Dev server: TUNNEL mode (works from any network)..."
  echo "  Scan the tunnel QR code below in Expo Go."
  echo "  For LAN only (local laptop): npm run start:lan"
  echo "=============================================="
  echo ""
  if [[ " $* " == *" --tunnel "* ]]; then
    exec npx expo start "$@"
  else
    exec npx expo start --tunnel "$@"
  fi
fi

echo "Dev server: LAN mode (only if Metro runs on YOUR laptop, same WiFi as phone)"
echo "=============================================="
echo ""
exec npx expo start "$@"
