#!/usr/bin/env bash
# ============================================================
# TukiPass — Guardrail CI: colores de tema en CSS/Blades
#
# Falla si se reintroducen colores de texto/superficie
# hardcodeados (grises de texto que rompen dark/light) en
# archivos de pagina y blades. Los valores permitidos son
# tokens var(--...) o blancos/naranjas de marca documentados.
#
# Uso:
#   scripts/audit-theme-colors.sh            # scan completo (CI)
#   scripts/audit-theme-colors.sh --report   # solo reporte
# ============================================================
set -u
cd "$(dirname "$0")/.." || exit 1
REPORT_ONLY=0
[ "${1:-}" = "--report" ] && REPORT_ONLY=1

# Grises de texto que NO deben aparecer en contextos de color
# (causan 2.7-4.4:1 en dark/light). Permitidos solo como
# comentario o valor de token base.
FORBIDDEN=(
  "color: #6b7280" "color:#6b7280"
  "color: #64748b" "color: #5b6472"
  "color: #5f5f5f" "color: #8a8a8a"
  "color: #6f7884" "color: #475569"
  "color: #94a3b8" "color: #cbd5e1"
  "color: #667184" "color: #697386"
  "color: #5b6b80" "color: #536075"
)

FILES=$(find public/assets/front/css -name '*.css' ! -name '*.min.css' \
  -exec grep -lE "color: ?#[0-9a-fA-F]{6}" {} + 2>/dev/null)

FAIL=0
TMP="$(mktemp)"
trap 'rm -f "$TMP"' EXIT

echo "== Scan de colores de texto hardcodeados (CSS front) =="
for pat in "${FORBIDDEN[@]}"; do
  HITS=$(grep -rn "$pat" public/assets/front/css resources/views 2>/dev/null \
    | grep -vE "\.min\.css|vendor|var\(--|:root|--muted-foreground|--foreground|HARDCODE-ALLOW" || true)
  if [ -n "$HITS" ]; then
    echo "$HITS" >> "$TMP"
  fi
done

if [ -s "$TMP" ]; then
  echo "COLORES DE TEXTO HARDCODEADOS DETECTADOS (P1):"
  cat "$TMP" | head -40
  [ "$REPORT_ONLY" = "0" ] && FAIL=1
else
  echo "OK"
fi

echo ""
echo "=========================================="
if [ "$FAIL" = "1" ]; then
  echo "RESULTADO: FAIL — colores de texto hardcodeados presentes"
  exit 1
fi
echo "RESULTADO: PASS"
exit 0
