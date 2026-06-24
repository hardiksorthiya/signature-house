#!/usr/bin/env bash
# Build standalone Android APK — install on phone, no Expo Go / QR / dev server needed.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

export ANDROID_HOME="${ANDROID_HOME:-/opt/android-sdk}"
export PATH="$ANDROID_HOME/platform-tools:$ANDROID_HOME/cmdline-tools/latest/bin:$PATH"

NODE_MAJOR="$(node -p "process.versions.node.split('.')[0]")"
if [ "$NODE_MAJOR" -lt 20 ]; then
  echo "Node 20+ required. Current: $(node -v)"
  exit 1
fi

echo "==> Standalone APK build (WebView → signature-in-house.com)"
echo "    Phone does NOT need same WiFi or this server's Metro bundler."
echo ""

if [ ! -d android ]; then
  echo "==> Generating native Android project..."
  npx expo prebuild --platform android --clean --no-install
fi

echo "==> Compiling release APK (first build may take 10+ minutes)..."
cd android
chmod +x gradlew
./gradlew assembleRelease --no-daemon

mkdir -p ../dist ../downloads
cp app/build/outputs/apk/release/app-release.apk ../dist/signature-in-house.apk
cp app/build/outputs/apk/release/app-release.apk ../downloads/signature-in-house.apk

echo ""
echo "=============================================="
echo "  BUILD SUCCESS"
echo "=============================================="
echo "APK:"
echo "  $ROOT/dist/signature-in-house.apk"
echo "  $ROOT/../downloads/signature-in-house.apk"
echo ""
echo "Install on Android:"
echo "  1. Copy APK to phone"
echo "  2. Tap to install (allow unknown sources if asked)"
echo "  3. Open app — loads your Laravel site directly"
echo "=============================================="
