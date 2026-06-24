#!/usr/bin/env bash
# Publish to EAS Update (preview channel) — open in Expo Go without running npm start.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

if [ -f .env.eas ]; then
  set -a
  # shellcheck disable=SC1091
  source .env.eas
  set +a
fi

if [ -f .env ]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

export EXPO_NO_REACT_NATIVE_DEVTOOLS="${EXPO_NO_REACT_NATIVE_DEVTOOLS:-1}"

echo "=============================================="
echo "  Publish to Expo Go (preview channel)"
echo "=============================================="
echo "Requires: npx eas-cli login && npx eas-cli init (one time)"
echo ""

if ! npx eas-cli whoami 2>/dev/null; then
  echo "Not logged in. Run: npm run eas:login"
  exit 1
fi

npx eas-cli update \
  --channel preview \
  --environment preview \
  --message "Signature In House preview" \
  --non-interactive

echo ""
echo "Done. Users with Expo Go can open your project from:"
echo "  https://expo.dev  → your project → Open in Expo Go"
echo ""
echo "Add project URL to Laravel .env:"
echo "  MOBILE_EXPO_PROJECT_URL=https://expo.dev/accounts/signaturetmservice/projects/signature-in-house"
echo "=============================================="
