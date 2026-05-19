#!/bin/sh
# TukiPass Queue Worker - Procesa facturas, emails y QR automaticamente
# Este script corre en loop infinito cada 10 segundos

echo "Iniciando worker de colas TukiPass..."
echo "Fecha: $(date)"
echo "========================================"

while true; do
    cd /app || exit 1
    php artisan queue:work --once --sleep=3 --tries=3
    sleep 10
done
