#!/usr/bin/env bash
# ============================================================
# TukiPass — sync main repo → Docker worktree (env local 8801)
#
# El stack Docker monta como /app el worktree:
#   ~/.config/superpowers/worktrees/tuki/codex-master-final
# (bind mount declarado en el docker-compose del stack local).
# El git de verdad vive en el repo principal; este script copia
# los archivos de la app que editamos hacia el worktree para que
# el navegador (127.0.0.1:8801) los sirva, y limpia views.
#
# Uso: bash scripts/sync-local-env.sh
# ============================================================
set -euo pipefail

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WT="${TUKI_WORKTREE:-$HOME/.config/superpowers/worktrees/tuki/codex-master-final}"

if [ ! -d "$WT/public" ]; then
  echo "ERROR: worktree no encontrado en $WT" >&2
  echo "Seteá TUKI_WORKTREE=/ruta/al/worktree si vive en otro lado." >&2
  exit 2
fi

SYNC_PATHS=(
  "app/Http/Controllers/BackEnd/Organizer"
  "app/Services"
  "public/assets/admin/css"
  "public/assets/admin/js"
  "public/assets/front/css"
  "public/assets/front/js"
  "resources/views/organizer"
  "resources/views/backend"
  "resources/views/partials"
  "resources/views/frontend"
)

for p in "${SYNC_PATHS[@]}"; do
  if [ -d "$SRC/$p" ]; then
    rsync -a "$SRC/$p/" "$WT/$p/"
  fi
done

if command -v docker >/dev/null 2>&1; then
  docker exec tuki-app-1 php artisan view:clear >/dev/null 2>&1 || true
fi

echo "Synced -> $WT (views cleared)"
