#!/usr/bin/env bash
# ============================================================
# TukiPass — Guardrail CI: Zero Hardcoded Data scan
#
# Falla (exit 1) si se introducen hardcodes criticos en zonas
# productivas (app/ config/ resources/views/ routes/ database/).
#
# Uso:
#   scripts/audit-hardcode.sh            # scan completo (CI)
#   scripts/audit-hardcode.sh --report   # solo reporte, exit 0
# ============================================================
set -u
cd "$(dirname "$0")/.." || exit 1

REPORT_ONLY=0
[ "${1:-}" = "--report" ] && REPORT_ONLY=1

FAIL=0
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

EXCLUDE='--exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=storage
--exclude-dir=.git --exclude-dir=app/Libraries/I18N --exclude='*.min.js' --exclude='*.min.css''

SCAN_DIRS="app config resources/views routes database scripts"

# ---------- 1. Secretos en produccion ----------
echo "== [1] Secretos =="
grep -rnE "APP_USR-[A-Za-z0-9]{20,}|sk_live_[A-Za-z0-9]{20,}|pk_live_[A-Za-z0-9]{20,}|AIza[0-9A-Za-z_-]{30,}" $SCAN_DIRS 2>/dev/null \
  | grep -vE '^\s*$' | grep -vE "#|//|\*" > "$TMP/secrets.txt"
if [ -s "$TMP/secrets.txt" ]; then
  echo "SECRETOS DETECTADOS (P0):"
  cat "$TMP/secrets.txt" | sed 's/=.*/=<REDACTED>/'
  FAIL=1
else
  echo "OK"
fi

# ---------- 2. URLs productivas hardcodeadas (fuera de config/routes) ----------
echo "== [2] URLs tukipass.com hardcodeadas =="
grep -rn "https://www.tukipass.com" app resources/views routes 2>/dev/null > "$TMP/urls.txt"
if [ -s "$TMP/urls.txt" ]; then
  echo "URLS PRODUCTIVAS DIRECTAS (P1):"
  cat "$TMP/urls.txt"
  FAIL=1
else
  echo "OK"
fi

# ---------- 3. Emails de contacto productivos en views/app ----------
echo "== [3] Emails de contacto hardcodeados =="
grep -rnE "soporte@tukipass\.com|info@tukipass\.com|hola@tukipass\.com|support@tukipass\.com" resources/views app 2>/dev/null \
  | grep -vE "config/tukipass" > "$TMP/emails.txt"
if [ -s "$TMP/emails.txt" ]; then
  echo "EMAILS PRODUCTIVOS (P1):"
  cat "$TMP/emails.txt"
  FAIL=1
else
  echo "OK"
fi

# ---------- 4. IDs productivos (find(N) en controllers) ----------
echo "== [4] IDs fijos en controllers =="
grep -rnE "find\([0-9]{2,}\)|where\('[a-z_]+_id',\s*[0-9]{2,}\)" app/Http/Controllers 2>/dev/null > "$TMP/ids.txt"
if [ -s "$TMP/ids.txt" ]; then
  echo "IDS FIJOS EN CONTROLLERS (P1):"
  cat "$TMP/ids.txt"
  FAIL=1
else
  echo "OK"
fi

# ---------- 5. Fallbacks de negocio inventados (?? dato real) ----------
# Solo datos de negocio (precio, %, moneda, email, fecha); los labels UI
# ('Sin estado', 'left', 'Organizador', etc.) no son hardcodes de negocio.
echo "== [5] Fallbacks de negocio inventados =="
grep -rnE "\?\? '(\$[0-9]|[0-9]+%|ARS|USD|EUR|[0-9]{4}-[0-9]{2}-[0-9]{2}|[a-z0-9._-]+@[a-z0-9.-]+\.)" app resources/views 2>/dev/null > "$TMP/fallbacks.txt"
if [ -s "$TMP/fallbacks.txt" ]; then
  echo "FALLBACKS SOSPECHOSOS (P2):"
  cat "$TMP/fallbacks.txt"
  [ "$REPORT_ONLY" = "0" ] && FAIL=1
else
  echo "OK"
fi

# ---------- 6. Precios/porcentajes literales en views ----------
echo "== [6] Precios/porcentajes literales =="
grep -rnE "\$[0-9]{2,}(\.[0-9]+)?\b|[0-9]+% ?descuento|descuento ?[0-9]+%" resources/views 2>/dev/null \
  | grep -vE "data-price|->price|money_format|price_format" > "$TMP/money.txt"
if [ -s "$TMP/money.txt" ]; then
  echo "PRECIOS/PORCENTAJES LITERALES EN VIEWS (P1):"
  cat "$TMP/money.txt"
  [ "$REPORT_ONLY" = "0" ] && FAIL=1
else
  echo "OK"
fi

# ---------- 7. Direcciones productivas en views ----------
echo "== [7] Direcciones conocidas =="
grep -rnE "Honduras 5535|Pueyrredón 1357" resources/views app 2>/dev/null \
  | grep -vE "AuditHardcodedData\.php|SeedColombiaWorldCupEvents\.php" > "$TMP/addr.txt"
if [ -s "$TMP/addr.txt" ]; then
  echo "DIRECCIONES HARDCODEADAS (P1):"
  cat "$TMP/addr.txt"
  [ "$REPORT_ONLY" = "0" ] && FAIL=1
else
  echo "OK"
fi

# ---------- 8. Scan semantico (patrones de negocio) via artisan ----------
echo "== [8] Scan semantico artisan (si hay entorno disponible) =="
if command -v php >/dev/null 2>&1 && [ -f "artisan" ] && php artisan list 2>/dev/null | grep -q "audit:hardcoded"; then
  if [ "$REPORT_ONLY" = "1" ]; then
    php artisan audit:hardcoded 2>&1 | tail -n +1
  else
    php artisan audit:hardcoded --fail 2>&1 | tail -n +1
    if [ "${PIPESTATUS[0]:-1}" != "0" ]; then
      FAIL=1
    fi
  fi
else
  echo "SKIP (sin entorno Laravel)"
fi

echo ""
echo "=========================================="
if [ "$FAIL" = "1" ]; then
  echo "RESULTADO: FAIL — hardcodes criticos detectados"
  exit 1
fi
echo "RESULTADO: PASS — cero hardcodes criticos"
exit 0
