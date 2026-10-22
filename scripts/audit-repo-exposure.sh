#!/usr/bin/env bash
# Falla si vuelven a trackearse dumps de instalador o adjuntos de usuarios.
set -euo pipefail
cd "$(dirname "$0")/.."

BAD=$(git ls-files | grep -E '^(public/installer/.*\.sql|public/assets/admin/img/(support-ticket|product/download_file)/.*\.zip)$' || true)

if [ -n "$BAD" ]; then
  echo "Archivos sensibles trackeados:"
  echo "$BAD"
  exit 1
fi

echo "Repositorio: sin dumps de instalador ni adjuntos ZIP trackeados."
