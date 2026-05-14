# Mailpit — Testing de emails local

## ¿Qué es Mailpit?

[Mailpit](https://github.com/axllent/mailpit) es un servidor SMTP de testing con interfaz web. Captura todos los emails que tu app envía localmente, sin llegar a destinatarios reales.

---

## Instalación

### macOS (Homebrew)

```bash
brew install mailpit
```

### Linux

Descargar binario:

```bash
sudo curl -fsSL https://raw.githubusercontent.com/axllent/mailpit/develop/install.sh | sudo bash
```

### Windows

Descargar `.exe` desde [releases](https://github.com/axllent/mailpit/releases) y ejecutar.

### Docker

```bash
docker run -d \
  --name=mailpit \
  -p 1025:1025 \
  -p 8025:8025 \
  axllent/mailpit
```

---

## Ejecución

### Desde terminal (sin Docker)

```bash
mailpit
```

Por defecto escucha:
- SMTP: `127.0.0.1:1025`
- Web UI: `http://127.0.0.1:8025`

### Con Docker Compose (opcional)

```yaml
services:
  mailpit:
    image: axllent/mailpit
    ports:
      - "1025:1025"
      - "8025:8025"
```

```bash
docker-compose up -d mailpit
```

---

## Configuración del proyecto

Copiá el archivo de ejemplo a `.env` (o mergeá manualmente):

```bash
cp .env.example.mailpit .env.mailpit
# Luego mergeá las líneas de MAIL_* a tu .env
```

O editá directamente tu `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=facturacion@tukipass.com
MAIL_FROM_NAME="${APP_NAME}"
```

Limpiá caché de config:

```bash
php artisan config:clear
```

---

## Interfaz web

Abrí [http://127.0.0.1:8025](http://127.0.0.1:8025) en tu navegador.

Allí vas a ver todos los emails capturados, incluyendo:
- Emails de facturación ARCA con PDF adjunto
- Notificaciones de reservas
- Emails de registro

---

## Producción

En producción no se usa Mailpit. Se recomienda **Postmark** (ya configurado en `config/mail.php` y `config/services.php`).

```env
MAIL_MAILER=postmark
POSTMARK_TOKEN=tu-token-de-postmark
MAIL_FROM_ADDRESS=facturacion@tukipass.com
MAIL_FROM_NAME="${APP_NAME}"
```

Postmark se usa para emails transaccionales (facturas, confirmaciones, etc.).

---

## Notas

- Asegurate de que no haya otro servicio usando el puerto 1025 o 8025.
- En macOS, si usás Docker Desktop, los puertos se bindan automáticamente a localhost.
- Para testing de facturación ARCA, enviá una factura de prueba y verificá que el PDF adjunto llegue correctamente a Mailpit.
