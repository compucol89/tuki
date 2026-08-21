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

# --- 4. raw colors en blades migrados (tokens obligatorios) --------------
# Los blades ya tokenizados NO deben reintroducir colores raw en su <style>.
MIGRATED_BLADES=(
  "organizer/event/index.blade.php"
  "organizer/event/edit.blade.php"
  "organizer/event/booking/index.blade.php"
  "organizer/event/booking/details.blade.php"
  "organizer/event/create.blade.php"
  "organizer/event/ticket/index.blade.php"
  "organizer/event/ticket/create.blade.php"
  "organizer/event/ticket/edit.blade.php"
  "organizer/telegram-bot/index.blade.php"
  "organizer/event/partials/ai-generate-button.blade.php"
)
check_blade_raw_colors() {
  local total=0
  local offenders=""
  for blade in "${MIGRATED_BLADES[@]}"; do
    [ -f "$ROOT/resources/views/$blade" ] || continue
    local n
    n=$(awk '/<style>/,/<\/style>/' "$ROOT/resources/views/$blade" \
        | grep -vE 'white-space|whitespace|box-shadow|text-shadow' \
        | grep -oE '#[0-9a-fA-F]{3,8}\b|rgba?\([0-9]|hsla?\([0-9]|\b(white|black)\b' \
        | wc -l | tr -d ' ')
    if [ "$n" -gt 0 ]; then
      total=$((total + n))
      offenders="$offenders\n    $blade ($n)"
    fi
  done
  local baseline_total
  baseline_total=$(python3 -c "import json;print(json.load(open('$BASELINE'))['blade_raw_colors'])")
  if [ "$total" -gt "$baseline_total" ]; then
    echo "FAIL [blade raw colors]: $total en blades migrados (baseline $baseline_total):$offenders"
    FAIL=1
  else
    echo "OK   [blade raw colors]: $total (baseline $baseline_total)"
  fi
}

# --- 5. outline:none / outline:0 en CSS propio ---------------------------
check_outline_suppression() {
  local css_files=(admin-main.css admin-skin.css)
  local total=0
  for f in "${css_files[@]}"; do
    [ -f "$ROOT/public/assets/admin/css/$f" ] || continue
    local n
    n=$(grep -cE 'outline:\s*(none|0)' "$ROOT/public/assets/admin/css/$f")
    total=$((total + n))
  done
  local baseline_total
  baseline_total=$(python3 -c "import json;print(json.load(open('$BASELINE'))['outline_suppressions'])")
  if [ "$total" -gt "$baseline_total" ]; then
    echo "FAIL [outline suppression]: $total (baseline $baseline_total)"
    FAIL=1
  else
    echo "OK   [outline suppression]: $total (baseline $baseline_total)"
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
  local blade_raw=0
  for blade in "${MIGRATED_BLADES[@]}"; do
    [ -f "$ROOT/resources/views/$blade" ] || continue
    n=$(awk '/<style>/,/<\/style>/' "$ROOT/resources/views/$blade" \
        | grep -vE 'white-space|whitespace|box-shadow|text-shadow' \
        | grep -oE '#[0-9a-fA-F]{3,8}\b|rgba?\([0-9]|hsla?\([0-9]|\b(white|black)\b' \
        | wc -l | tr -d ' ')
    blade_raw=$((blade_raw + n))
  done
  local outline=0
  for f in admin-main.css admin-skin.css; do
    n=$(grep -cE 'outline:\s*(none|0)' "$ROOT/public/assets/admin/css/$f")
    outline=$((outline + n))
  done
  python3 - "$inline_files" "$hardcoded" "$important" "$blade_raw" "$outline" << 'PYEOF'
import json, sys
baseline = {
    "generated": "2026-08-21",
    "inline_styles": json.loads(sys.argv[1]),
    "hardcoded_surfaces": int(sys.argv[2]),
    "important_count": int(sys.argv[3]),
    "blade_raw_colors": int(sys.argv[4]),
    "outline_suppressions": int(sys.argv[5]),
}
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
    check_blade_raw_colors
    check_outline_suppression
    echo ""
    if [ "$FAIL" -eq 0 ]; then
      echo "PASS — sin deuda de theming nueva"
    else
      echo "FAIL — deuda nueva detectada (ver docs/auditorias/css-theme/14-static-guards.md)"
    fi
    exit $FAIL
    ;;
esac
