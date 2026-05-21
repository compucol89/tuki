#!/bin/bash
# =============================================================================
# Script de despliegue de hardening de seguridad para TukiPass
# Uso: Ejecutar en EasyPanel (o cualquier servidor Linux con Apache)
# Objetivo: Aplicar los mismos cambios de seguridad que en local
# =============================================================================

set -euo pipefail

PROJECT_DIR="${1:-.}"
cd "$PROJECT_DIR"

echo "=========================================="
echo "TukiPass Security Hardening Deploy Script"
echo "=========================================="
echo "Directorio: $(pwd)"
echo ""

# ─── Verificar archivos existen ───
for f in .htaccess public/.htaccess config/cors.php .gitignore; do
    if [[ ! -f "$f" ]]; then
        echo "ERROR: No se encontró $f — ¿estás en la raíz del proyecto?"
        exit 1
    fi
done

# ─── Backup ───
BACKUP_DIR=".security-backup-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp .htaccess "$BACKUP_DIR/.htaccess.bak"
cp public/.htaccess "$BACKUP_DIR/public-htaccess.bak"
cp config/cors.php "$BACKUP_DIR/cors.php.bak"
cp .gitignore "$BACKUP_DIR/gitignore.bak"
echo "Backup guardado en: $BACKUP_DIR"
echo ""

# ─── PASO 1: .htaccess raíz ───
echo "[1/4] Aplicando bloqueo de .git/ y archivos sensibles en .htaccess raíz..."
if ! grep -q "BLOQUEO DE ARCHIVOS Y DIRECTORIOS SENSIBLES" .htaccess; then
    # Crear archivo temporal con el bloqueo al inicio
    cat > /tmp/htaccess_security_block.txt << 'HTEOF'
# ─── BLOQUEO DE ARCHIVOS Y DIRECTORIOS SENSIBLES ───
<IfModule mod_rewrite.c>
    RewriteEngine On
    # Bloquear acceso a .git/ completo
    RewriteRule (^\.git/) - [F,L]
</IfModule>
# Bloquear archivos sensibles
<FilesMatch "^\.env">
    Require all denied
</FilesMatch>
<FilesMatch "\.(sql|gz|tar|zip|bak|backup|old|dump|pem|key|crt|p12|pfx)$">
    Require all denied
</FilesMatch>
<FilesMatch "^(composer\.lock|package-lock\.json|yarn\.lock)$">
    Require all denied
</FilesMatch>

HTEOF
    # Insertar al inicio del archivo existente
    cat /tmp/htaccess_security_block.txt .htaccess > /tmp/.htaccess.new
    mv /tmp/.htaccess.new .htaccess
    rm -f /tmp/htaccess_security_block.txt
    echo "      OK — Bloqueo agregado al inicio de .htaccess"
else
    echo "      SKIP — Ya existe bloqueo de seguridad"
fi

# ─── PASO 2: config/cors.php ───
echo "[2/4] Restringiendo CORS en config/cors.php..."
if grep -q "'allowed_origins' => \['*'\]" config/cors.php; then
    sed -i "s/'allowed_methods' => \['\*'\],/'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],/" config/cors.php
    sed -i "s/'allowed_origins' => \['\*'\],/'allowed_origins' => [\n        env('APP_URL', 'https:\/\/tukipass.com'),\n        'https:\/\/tukipass.com',\n        'https:\/\/www.tukipass.com',\n    ],/" config/cors.php
    sed -i "s/'allowed_headers' => \['\*'\],/'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'X-CSRF-TOKEN'],/" config/cors.php
    echo "      OK — CORS restringido a dominios de TukiPass"
else
    echo "      SKIP — CORS ya está restringido"
fi

# ─── PASO 3: .gitignore ───
echo "[3/4] Actualizando .gitignore..."
if ! grep -q "Copia de .env" .gitignore; then
    cat >> .gitignore << 'GIEOF'

# ── Backups y copias de .env ──────────────────────────────────────────────────
Copia de .env
"Copia de .env"
.env.copy
.env.bak
.env.save
.env.dist
# ── Dumps SQL sueltos en raíz ────────────────────────────────────────────────
/*.sql
!database/*.sql
# ── Documentos generados ────────────────────────────────────────────────────
docs/generated/*.sql
# ── Worktrees de Claude ─────────────────────────────────────────────────────
.claude/worktrees/
GIEOF
    echo "      OK — Patrones de seguridad agregados a .gitignore"
else
    echo "      SKIP — Patrones ya existen"
fi

# ─── PASO 4: public/.htaccess ───
echo "[4/4] Agregando security headers en public/.htaccess..."
if ! grep -q "Security Headers" public/.htaccess; then
    cat >> public/.htaccess << 'PHTEOF'

# ─── Security Headers ───
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains" env=HTTPS
</IfModule>
PHTEOF
    echo "      OK — Security headers agregados"
else
    echo "      SKIP — Security headers ya existen"
fi

# ─── Validación ───
echo ""
echo "=========================================="
echo "Validación de cambios"
echo "=========================================="

# Verificar PHP syntax
php -l config/cors.php || { echo "ERROR: Sintaxis PHP inválida"; exit 1; }

# Verificar que .htaccess raíz tiene la regla
if grep -q "RewriteRule (^\\.git/) - \[F,L\]" .htaccess; then
    echo "✓ .htaccess raíz: bloqueo .git/ confirmado"
else
    echo "✗ .htaccess raíz: FALTA bloqueo .git/"
    exit 1
fi

# Verificar que public/.htaccess tiene headers
if grep -q "X-Frame-Options" public/.htaccess; then
    echo "✓ public/.htaccess: security headers confirmados"
else
    echo "✗ public/.htaccess: FALTAN security headers"
    exit 1
fi

# Verificar que CORS está restringido
if ! grep -q "'allowed_origins' => \['*'\]" config/cors.php; then
    echo "✓ config/cors.php: CORS restringido confirmado"
else
    echo "✗ config/cors.php: CORS sigue con wildcard"
    exit 1
fi

# Verificar .gitignore
if grep -q "Copia de .env" .gitignore; then
    echo "✓ .gitignore: patrones de seguridad confirmados"
else
    echo "✗ .gitignore: FALTAN patrones"
    exit 1
fi

echo ""
echo "=========================================="
echo "DESPLIEGUE COMPLETADO CON ÉXITO"
echo "=========================================="
echo "Backup disponible en: $BACKUP_DIR"
echo ""
echo "Rollback (si es necesario):"
echo "  cp $BACKUP_DIR/.htaccess.bak .htaccess"
echo "  cp $BACKUP_DIR/public-htaccess.bak public/.htaccess"
echo "  cp $BACKUP_DIR/cors.php.bak config/cors.php"
echo "  cp $BACKUP_DIR/gitignore.bak .gitignore"
echo ""
echo "IMPORTANTE: Este script NO elimina archivos existentes."
echo "Para eliminar public/installer/ y SQL dumps, hazlo manualmente:"
echo "  rm -rf public/installer/"
echo "  rm -f tukipass.sql eventos.sql docs/generated/eventos.sql"
echo "  rm -f 'Copia de .env'"
