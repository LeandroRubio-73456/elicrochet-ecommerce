# Instalación y operación

Guía complementaria del [README](../README.md): instalación con Docker, uso de MySQL, pasarela de pago, correo y solución de problemas.

## Instalación con Docker / Laravel Sail

Este repositorio no trae `compose.yaml` commiteado (es un archivo generado), así que la primera vez hay que generarlo con `sail:install` antes de poder levantar los contenedores:

```bash
git clone https://github.com/LeandroRubio-73456/elicrochet-ecommerce.git
cd elicrochet-ecommerce
cp .env.example .env

# Instala dependencias con un contenedor temporal (no requiere PHP local)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install

# Genera compose.yaml eligiendo los servicios (ejemplo: mysql y redis)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest php artisan sail:install --with=mysql,redis

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

`sail:install` ajusta automáticamente el `.env` para usar MySQL (host, usuario y contraseña de Sail). Abre `http://localhost`.

> Si ya tienes otro proyecto Sail corriendo (por ejemplo en el puerto 80 o 3306), define `APP_PORT`, `FORWARD_DB_PORT`, `FORWARD_REDIS_PORT` y `VITE_PORT` en el `.env` antes de `sail up` para evitar conflictos de puertos.

Para desarrollo con recarga en caliente:

```bash
./vendor/bin/sail npm run dev
```

## Usar MySQL o MariaDB sin Docker

El proyecto usa SQLite por defecto. Para usar MySQL o MariaDB, configura las variables `DB_*` en `.env` y ejecuta:

```bash
php artisan migrate --seed
```

## Pasarela de pago (PayPhone)

El checkout usa PayPhone. Si no hay credenciales reales de comercio configuradas (`PAYPHONE_TOKEN` vacío, el caso por defecto en `.env.example`), el sistema simula la pasarela automáticamente: en vez de redirigir a PayPhone, muestra una pantalla propia de "Modo simulación" con botones para aprobar o rechazar el pago. El resto del flujo (creación de orden, control de stock, correos) se ejecuta exactamente igual que con un pago real.

Esto se controla con la variable `PAYPHONE_SIMULATE` en `.env`:

```env
PAYPHONE_SIMULATE=true   # fuerza el modo simulado
PAYPHONE_SIMULATE=false  # fuerza llamadas reales a la API de PayPhone
# (vacío = automático: simula solo si PAYPHONE_TOKEN está vacío)
```

## Correo

Con Sail, los correos se capturan en Mailpit (`http://localhost:8025`), sin necesidad de configurar ningún proveedor externo. Sin Docker, cambia `MAIL_MAILER=log` en `.env` para revisar los correos en `storage/logs/laravel.log`.

## Pruebas con Sail

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
```

## Solución rápida de problemas

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan storage:link
```

Si aparece un error de permisos en Docker, ejecuta los comandos Artisan dentro del contenedor mediante Sail y verifica que la carpeta del proyecto tenga permisos de escritura para `storage` y `bootstrap/cache`.

## Seguridad

Las credenciales de los seeders son únicamente para demostración. En una instalación real cambia las contraseñas, configura `APP_DEBUG=false`, usa HTTPS, define credenciales reales de PayPhone y un correo SMTP seguro.
