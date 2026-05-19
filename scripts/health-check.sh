#!/bin/bash
# TukiPass — Script de Diagnóstico de Producción
# Uso: ./scripts/health-check.sh

echo "═══════════════════════════════════════════════════════════════"
echo "  TUKIPASS — DIAGNÓSTICO DE SISTEMA"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: No se encontró artisan. Ejecutar desde la raíz del proyecto.${NC}"
    exit 1
fi

echo "📋 Verificando entorno..."
echo "  PHP: $(php -v | head -1)"
echo "  Laravel: $(php artisan --version 2>/dev/null || echo 'No disponible')"
echo ""

echo "🔍 Ejecutando diagnóstico completo..."
echo ""

php artisan tukipass:health-check

EXIT_CODE=$?

echo ""
echo "═══════════════════════════════════════════════════════════════"

if [ $EXIT_CODE -eq 0 ]; then
    echo -e "${GREEN}✅ Diagnóstico completado sin errores${NC}"
else
    echo -e "${RED}❌ Diagnóstico completado con errores${NC}"
fi

echo "═══════════════════════════════════════════════════════════════"

exit $EXIT_CODE
