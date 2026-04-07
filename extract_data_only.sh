#!/bin/bash
# Extract only INSERT statements from a full phpMyAdmin dump.
# Use this when tables already exist (e.g. from Laravel migrations) and you
# only want to load the data.
#
# Usage:
#   1. Save your full phpMyAdmin dump as testsignature_dump.sql in this directory.
#   2. Run: bash extract_data_only.sh
#   3. Import data: mysql -u signaturefinal -p signaturefinal < testsignature_data_only.sql

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DUMP_FILE="${SCRIPT_DIR}/testsignature_dump.sql"
OUTPUT_FILE="${SCRIPT_DIR}/testsignature_data_only.sql"

if [[ ! -f "$DUMP_FILE" ]]; then
  echo "Error: testsignature_dump.sql not found in this directory."
  echo "Save your full phpMyAdmin export as: testsignature_dump.sql"
  exit 1
fi

echo "Extracting INSERT statements from $DUMP_FILE ..."

{
  echo "-- Data-only export for signaturefinal (tables must already exist)"
  echo "SET FOREIGN_KEY_CHECKS = 0;"
  echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';"
  echo ""
  # Extract INSERT statements (single-line or multi-line ending with ;)
  awk '
    /^INSERT INTO/ {
      buf = $0
      while (buf !~ /;\s*$/) { if (getline <= 0) break; buf = buf "\n" $0 }
      print buf
      next
    }
  ' "$DUMP_FILE"
  echo ""
  echo "SET FOREIGN_KEY_CHECKS = 1;"
} > "$OUTPUT_FILE"

echo "Created: $OUTPUT_FILE"
echo ""
echo "To load data into signaturefinal (tables already migrated):"
echo "  mysql -u signaturefinal -p signaturefinal < testsignature_data_only.sql"
echo ""
echo "If you get duplicate key errors, tables are not empty. Optionally truncate first:"
echo "  mysql -u signaturefinal -p signaturefinal -e \"SET FOREIGN_KEY_CHECKS=0; \$(mysql -u signaturefinal -p signaturefinal -N -e \"SELECT CONCAT('TRUNCATE TABLE \\\`', table_name, '\\\`;\') FROM information_schema.tables WHERE table_schema=DATABASE();\") ; SET FOREIGN_KEY_CHECKS=1;\""
echo "  Then run: mysql ... < testsignature_data_only.sql"
