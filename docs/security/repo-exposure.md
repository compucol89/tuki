# Exposición del repositorio — 2026-10-22

## Hallazgo confirmado

El repositorio `compucol89/tuki` es público. Hasta esta fecha estaban trackeados:

- `public/installer/database.sql` (1,3 MB): dump de instalador con **14 emails únicos** y **8 hashes bcrypt**. No se encontraron claves de pago ni `APP_KEY`.
- Cinco ZIP en `public/assets/admin/img/support-ticket/` y uno en `public/assets/admin/img/product/download_file/`: contienen imágenes (EPS/JPG/PNG), no datos personales.
- `tukipass.sql` y `.env` **no** estaban trackeados; raw GitHub devuelve 404 para ambos.
- El acceso HTTP a `installer/database.sql` y a los ZIP devuelve 404/403 en producción: la exposición era por GitHub, no por HTTP.

## Acción tomada

- Se dejaron de trackear `public/installer/database.sql` y los ZIP.
- `.gitignore` bloquea `/public/installer/*.sql`, `/public/assets/admin/img/support-ticket/*.zip` y `/public/assets/admin/img/product/download_file/*.zip`.
- `scripts/audit-repo-exposure.sh` falla en CI si vuelven a trackearse.

## Pendiente

1. **Historial Git.** El archivo sigue en commits anteriores. Para eliminarlo:
   - opción A: privatizar el repositorio;
   - opción B: reescribir historial con `git filter-repo` y force push coordinado (requiere autorización explícita y backup).
2. **Rotar credenciales demo.** Si los emails/hashes del installer son reales, rotar contraseñas y notificar. Si son demo, documentarlo.
3. **Verificar GitHub Secret Scanning** y branch protection (requiere autenticación).
4. **Revisar adjuntos de soporte.** Los ZIP se sirven desde `public/`; si contienen documentación sensible, moverlos a `storage/` con descarga autenticada.
