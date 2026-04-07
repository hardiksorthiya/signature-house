#!/usr/bin/env bash
# Laravel storage must be writable by the PHP-FPM user (this site: signatureinhouse).
# If you run `php artisan` or `composer` as root, compiled views become root-owned and
# the next request that recompiles Blade will fail with:
#   file_put_contents(.../storage/framework/views/...): Permission denied → HTTP 500
#
# Run after deploy or whenever views/logs return permission errors:
#   sudo bash scripts/fix-storage-permissions.sh
#
# Optional stronger fix (new root-created files still writable by FPM user): install ACL
# tools and run once with USE_STORAGE_ACL=1:
#   sudo apt install -y acl
#   sudo USE_STORAGE_ACL=1 bash scripts/fix-storage-permissions.sh
#
# From project root (signature-laravel/), or pass absolute path to this script.

set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OWNER="${DEPLOY_OWNER:-signatureinhouse}"
GROUP="${DEPLOY_GROUP:-signatureinhouse}"
# Set USE_STORAGE_ACL=1 to apply default ACLs (needs `setfacl`; e.g. apt install acl).
USE_STORAGE_ACL="${USE_STORAGE_ACL:-0}"

if [[ "${EUID:-0}" -ne 0 ]]; then
  echo "This script must run as root (e.g. sudo bash $0)" >&2
  exit 1
fi

chown -R "$OWNER:$GROUP" "$ROOT/storage" "$ROOT/bootstrap/cache"
find "$ROOT/storage" -type d -exec chmod 775 {} \;
find "$ROOT/storage" -type f -exec chmod 664 {} \;
find "$ROOT/bootstrap/cache" -type d -exec chmod 775 {} \;
find "$ROOT/bootstrap/cache" -type f -exec chmod 664 {} \;

apply_default_acls() {
  command -v setfacl >/dev/null 2>&1 || return 1
  # Existing tree: explicit ACL for FPM user
  setfacl -R -m "u:${OWNER}:rwx" "$ROOT/storage" "$ROOT/bootstrap/cache"
  # New files/dirs under these paths inherit write for FPM user (even if created by root)
  find "$ROOT/storage" "$ROOT/bootstrap/cache" -type d -exec setfacl -d -m "u:${OWNER}:rwx" {} \;
  return 0
}

if [[ "$USE_STORAGE_ACL" == "1" ]]; then
  if apply_default_acls; then
    echo "OK: default ACLs applied so ${OWNER} keeps rwx on new files under storage and bootstrap/cache"
  else
    echo "Note: USE_STORAGE_ACL=1 but setfacl not found — install acl (e.g. apt install acl) for this feature." >&2
  fi
fi

echo "OK: $ROOT/storage and bootstrap/cache → $OWNER:$GROUP (dirs 775, files 664)"
echo "Tip: run Artisan/Composer as the deploy user, not root: sudo -u $OWNER php artisan ..."
echo "Tip: deploy.sh runs commands as $OWNER automatically when invoked as root."
