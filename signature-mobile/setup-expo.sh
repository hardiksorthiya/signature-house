#!/usr/bin/env bash
# One-time Expo + EAS setup for Expo Go and future native builds.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

echo "=============================================="
echo "  Expo setup — Signature In House Mobile"
echo "=============================================="
echo ""
echo "Step 1: Create free account at https://expo.dev"
echo "Step 2: Login"
npx eas-cli login
echo ""
echo "Step 3: Link this project to Expo"
npx eas-cli init
echo ""
echo "Step 4: Copy project ID into .env"
if [ ! -f .env ]; then
  cp .env.example .env
fi
echo "  Add EAS_PROJECT_ID=... and EXPO_OWNER=... to .env"
echo ""
echo "Step 5: Publish for Expo Go (no dev server needed)"
echo "  npm run publish:preview"
echo ""
echo "Step 6 (later): Native APK / TestFlight"
echo "  npm run build:android"
echo "  npm run build:ios:testflight"
echo "=============================================="
