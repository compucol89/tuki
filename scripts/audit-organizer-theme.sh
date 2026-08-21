#!/usr/bin/env bash
# ============================================================
# TukiPass — Organizer Theme Audit (NO NEW DEBT gate)
#
# Detecta regresiones de theming en CSS/Blades del Organizer:
#   1. <style> inline nuevo en blades (sin allowlist)
#   2. colores de superficie hardcodeados nuevos en CSS propio
#   3. !important nuevo en CSS propio (sin allowlist)
#
# Uso: bash scripts/audit-organizer-theme.sh [--report]
# Exit 0 = sin deuda nueva · Exit 1 = deuda nueva detectada
# ============================================================
set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BASELINE="$ROOT/scripts/baseline-theme.json"
REPORT="$ROOT/docs/auditorias/css-theme/14-static-guards.md"
FAIL=0

require_baseline() {
  if [ ! -f "$BASELINE" ]; then
    echo "ERROR: baseline no encontrado ($BASELINE). Generar con --init" >&2
    exit 2
  fi
}

# --- 1. <style> inline en blades del Organizer -------------------------
check_inline_styles() {
  local current allowlisted new_files
  current=$(find "$ROOT/resources/views/organizer" -name '*.blade.php' -exec grep -l '<style>' {} + | sed "s|$ROOT/||" | sort)
  allowlisted=$(python3 -c "import json;print('\n'.join(json.load(open('$BASELINE'))['inline_styles']))")
  new_files=$(comm -23 <(echo "$current") <(echo "$allowlisted"))
  if [ -n "$new_files" ]; then
    echo "FAIL [inline <style>]: nuevos blades con CSS inline:"
    echo "$new_files" | sed 's/^/    /'
    FAIL=1
  else
    echo "OK   [inline <style>]: sin blades nuevos con CSS inline"
  fi
}

# --- 2. hardcoded surfaces en CSS propio -------------------------------
check_hardcoded_colors() {
  local css_files=(admin-main.css admin-skin.css theme-dark.css)
  local total=0
  for f in "${css_files[@]}"; do
    [ -f "$ROOT/public/assets/admin/css/$f" ] || continue
    # superficies claras hardcodeadas en background/border (NO color de texto:
    # #ffffff como color de texto sobre un botón naranja es legítimo).
    # Excluye la definición de tokens (--xxx: #fff).
    local n
    n=$(grep -E 'background(-color)?:|border(-[a-z]+)?:' "$ROOT/public/assets/admin/css/$f" \
        | grep -oE '#(f[0-9a-f]{5}|ffffff|fbfcfe|f8fafc|eef1f5|e7eaf0)\b' | wc -l | tr -d ' ')
    total=$((total + n))
  done
  local baseline_total
  baseline_total=$(python3 -c "import json;print(json.load(open('$BASELINE'))['hardcoded_surfaces'])")
  if [ "$total" -gt "$baseline_total" ]; then
    echo "FAIL [hardcoded]: $total superficies claras (baseline $baseline_total)"
    FAIL=1
  else
    echo "OK   [hardcoded]: $total superficies claras (baseline $baseline_total)"
  fi
}

# --- 3. !important en CSS propio ----------------------------------------
check_important() {
  local css_files=(admin-main.css admin-skin.css theme-dark.css)
  local total=0
  for f in "${css_files[@]}"; do
    [ -f "$ROOT/public/assets/admin/css/$f" ] || continue
    local n
    n=$(grep -c '!important' "$ROOT/public/assets/admin/css/$f")
    total=$((total + n))
  done
  local baseline_total
  baseline_total=$(python3 -c "import json;print(json.load(open('$BASELINE'))['important_count'])")
  if [ "$total" -gt "$baseline_total" ]; then
    echo "FAIL [!important]: $total (baseline $baseline_total) — justificar nuevos"
    FAIL=1
  else
    echo "OK   [!important]: $total (baseline $baseline_total)"
  fi
}

# --- init: genera baseline ----------------------------------------------
init_baseline() {
  local inline_files
  inline_files=$(find "$ROOT/resources/views/organizer" -name '*.blade.php' -exec grep -l '<style>' {} + | sed "s|$ROOT/||" | python3 -c "import sys,json;print(json.dumps(sorted(sys.stdin.read().splitlines())))")
  local hardcoded=0
  for f in admin-main.css admin-skin.css theme-dark.css; do
    n=$(grep -oE '#(f[0-9a-f]{5}|ffffff|fbfcfe|f8fafc|eef1f5|e7eaf0)' "$ROOT/public/assets/admin/css/$f" | wc -l | tr -d ' ')
    hardcoded=$((hardcoded + n))
  done
  local important=0
  for f in admin-main.css admin-skin.css theme-dark.css; do
    n=$(grep -c '!important' "$ROOT/public/assets/admin/css/$f")
    important=$((important + n))
  done
  python3 - "$inline_files" "$hardcoded" "$important" << 'PYEOF'
import json, sys
baseline = {
    "generated": "2026-08-21",
    "inline_styles": json.loads(sys.argv[1]),
    "hardcoded_surfaces": int(sys.argv[2]),
    "important_count": int(sys.argv[3]),
}
with open(sys.argv[0] if False else "/dev/stdout", "w") as f:
    pass
print(json.dumps(baseline, indent=2))
PYEOF
  echo "Baseline (preview arriba). Guardar como: scripts/baseline-theme.json"
}

case "${1:-}" in
  --init) init_baseline ;;
  *)
    require_baseline
    check_inline_styles
    check_hardcoded_colors
    check_important
    echo ""
    if [ "$FAIL" -eq 0 ]; then
      echo "PASS — sin deuda de theming nueva"
    else
      echo "FAIL — deuda nueva detectada (ver docs/auditorias/css-theme/14-static-guards.md)"
    fi
    exit $FAIL
    ;;
esac
