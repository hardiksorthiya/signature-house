#!/usr/bin/env bash
# Build iOS app on Expo EAS cloud and submit to TestFlight.
# Requires: Apple Developer Program ($99/yr), Expo account (free), credentials below.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

ENV_FILE="$ROOT/.env.eas"
if [ -f "$ENV_FILE" ]; then
  set -a
  # shellcheck disable=SC1090
  source "$ENV_FILE"
  set +a
fi

NODE_MAJOR="$(node -p "process.versions.node.split('.')[0]")"
if [ "$NODE_MAJOR" -lt 20 ]; then
  echo "Node 20+ required. Current: $(node -v)"
  exit 1
fi

EAS="npx eas-cli"

echo "=============================================="
echo "  iOS TestFlight build (EAS + App Store Connect)"
echo "=============================================="
echo ""

if [ -z "${EXPO_TOKEN:-}" ]; then
  echo "Step 1 — Expo login (one time)"
  echo "  npx eas-cli login"
  echo "  Or set EXPO_TOKEN in .env.eas (expo.dev → Access Tokens)"
  echo ""
  if ! $EAS whoami 2>/dev/null; then
    echo "Not logged in to Expo. Run: npx eas-cli login"
    exit 1
  fi
else
  export EXPO_TOKEN
  echo "Using EXPO_TOKEN from .env.eas"
fi

if [ -z "${EAS_PROJECT_ID:-}" ]; then
  echo "Step 2 — Link project to Expo (one time)"
  echo "  npx eas-cli init"
  echo "  Then copy project ID into .env.eas as EAS_PROJECT_ID"
  echo "  and into app.config.js extra.eas.projectId if needed."
  echo ""
  if ! $EAS project:info 2>/dev/null | head -5; then
    echo "Project not linked. Run: npx eas-cli init"
    exit 1
  fi
else
  export EAS_PROJECT_ID
fi

echo ""
echo "Step 3 — Apple credentials (one time)"
echo "  Create app in App Store Connect with bundle ID: com.signatureinhouse.app"
echo "  Then run: npx eas-cli credentials"
echo "  Or set APPLE_ID, APPLE_TEAM_ID, APPLE_ASC_APP_ID in .env.eas"
echo ""

echo "==> Starting iOS cloud build (profile: testflight)..."
echo "    This runs on Expo servers (no Mac required). Usually 15–30 minutes."
echo ""

$EAS build --platform ios --profile testflight --non-interactive ${EAS_BUILD_FLAGS:-}

echo ""
echo "==> Build finished. Submitting to TestFlight..."

if [ -n "${APPLE_ID:-}" ] && [ -n "${APPLE_ASC_APP_ID:-}" ]; then
  SUBMIT_ARGS=(--platform ios --latest --profile testflight --non-interactive)
  if [ -n "${APPLE_APP_SPECIFIC_PASSWORD:-}" ]; then
    SUBMIT_ARGS+=(--apple-id "$APPLE_ID" --apple-app-specific-password "$APPLE_APP_SPECIFIC_PASSWORD")
  fi
  $EAS submit "${SUBMIT_ARGS[@]}"
else
  echo "Skipping auto-submit (set APPLE_ID + APPLE_ASC_APP_ID in .env.eas)."
  echo "Submit manually:"
  echo "  npx eas-cli submit --platform ios --latest --profile testflight"
fi

echo ""
echo "=============================================="
echo "  NEXT STEPS"
echo "=============================================="
echo "1. Open https://appstoreconnect.apple.com → TestFlight"
echo "2. Add internal/external testers"
echo "3. Copy public invite link (TestFlight → Public Link)"
echo "4. Add to Laravel .env:"
echo "   MOBILE_IOS_TESTFLIGHT_URL=https://testflight.apple.com/join/XXXX"
echo "5. Users open that link on iPhone to install the native app"
echo "=============================================="
