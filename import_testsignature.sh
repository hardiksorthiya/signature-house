#!/bin/bash
# Import testsignature dump into signaturefinal database
# Usage: Save your phpMyAdmin dump as testsignature_dump.sql in this directory, then run: bash import_testsignature.sh

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DUMP_FILE="${SCRIPT_DIR}/testsignature_dump.sql"
MODIFIED_DUMP="${SCRIPT_DIR}/testsignature_modified.sql"

DB_USER="signaturefinal"
DB_PASS="Signature@2025"
DB_HOST="127.0.0.1"
DB_NAME="signaturefinal"

if [[ ! -f "$DUMP_FILE" ]]; then
  echo "Error: Save your SQL dump as: testsignature_dump.sql"
  echo "  (Paste your phpMyAdmin export into a file named testsignature_dump.sql in this folder)"
  exit 1
fi

echo "Preparing dump for database: $DB_NAME"
# Remove CREATE DATABASE and USE testsignature; use signaturefinal instead
# Use CREATE TABLE IF NOT EXISTS so duplicate table definitions in dump do not fail
# Prepend/append FOREIGN_KEY_CHECKS=0/1 so constraint order and data don't fail import
{
  echo "SET FOREIGN_KEY_CHECKS = 0;"
  echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';"
  sed -e 's/CREATE DATABASE IF NOT EXISTS `testsignature`[^;]*;//' \
      -e 's/USE `testsignature`;/USE `signaturefinal`;/' \
      -e 's/CREATE TABLE `/CREATE TABLE IF NOT EXISTS `/g' \
      -e '/^CREATE DATABASE IF NOT EXISTS/d' \
      -e '/^USE `signaturefinal`;/d' \
      "$DUMP_FILE"
  echo "SET FOREIGN_KEY_CHECKS = 1;"
} > "$MODIFIED_DUMP"

echo "Dropping and recreating database $DB_NAME..."
mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "Importing dump..."
mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME" < "$MODIFIED_DUMP"

echo "Done. Database $DB_NAME has been replaced with data from your dump."
rm -f "$MODIFIED_DUMP"
