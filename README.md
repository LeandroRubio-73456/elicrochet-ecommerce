# EliCrochet Ecommerce

<p align="center">
  <img src="public/assets/images/Logo.webp" alt="EliCrochet Logo" width="200">
</p>

<p align="center">
  <a href="https://github.com/LeandroRubio-73456/elicrochet-ecommerce/actions/workflows/ci.yml">
    <img src="https://github.com/LeandroRubio-73456/elicrochet-ecommerce/actions/workflows/ci.yml/badge.svg" alt="CI Quality Gate">
  </a>
  <a href="https://sonarcloud.io/summary/new_code?id=LeandroRubio-73456_elicrochet-ecommerce">
    <img src="https://sonarcloud.io/api/project_badges/measure?project=LeandroRubio-73456_elicrochet-ecommerce&metric=alert_status" alt="Quality Gate Status">
  </a>
  <a href="https://sonarcloud.io/summary/new_code?id=LeandroRubio-73456_elicrochet-ecommerce">
    <img src="https://sonarcloud.io/api/project_badges/measure?project=LeandroRubio-73456_elicrochet-ecommerce&metric=coverage" alt="Coverage">
  </a>
  <a href="https://opensource.org/licenses/MIT">
    <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License: MIT">
  </a>
</p>

Solución de comercio electrónico desarrollada para EliCrochet, una microempresa de artesanías en Ecuador. Digitaliza un catálogo e inventario que antes se manejaba manualmente por WhatsApp: carrito, checkout con pasarela de pago, pedidos totalmente personalizados por cotización, reseñas de producto y un panel administrativo con reportes financieros.

## Capturas

<p align="center">
  <img src="public/screenshots/home.png" alt="Página de inicio" width="45%">
  <img src="public/screenshots/admin.png" alt="Panel de administración" width="45%">
</p>

## Stack tecnológico

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
</p>

- Laravel 12 / PHP 8.4+
- MySQL o SQLite (SQLite es el motor por defecto para desarrollo local sin Docker)
- Blade + Bootstrap 5
- Pest/PHPUnit (pruebas automatizadas), Laravel Pint (estilo de código)
- CI en GitHub Actions + análisis estático en SonarCloud
- Docker / Laravel Sail para el entorno de desarrollo

## Funciones principales

- Catálogo de productos por categorías, con especificaciones dinámicas configurables por categoría.
- Carrito de compras y checkout con pasarela de pago PayPhone (con modo simulado para desarrollo, ver más abajo).
- Pedidos totalmente personalizados: el cliente solicita una cotización con imágenes de referencia, el admin la valora y el cliente paga desde su cuenta.
- Sistema de reseñas: solo puede reseñar quien compró y recibió el producto.
- Autenticación propia y con Google (Socialite).
- Panel de administración: productos, categorías, usuarios, órdenes, reportes financieros exportables.
- Notificaciones por correo transaccionales (confirmación de pago, cambios de estado, cotizaciones).
- Control de stock con transacciones seguras ante compras concurrentes.

## Requisitos

- PHP 8.4 o superior.
- Composer 2.
- Node.js 20 o superior y npm.
- MySQL 8 o SQLite.
- Git.
- Para Docker: Docker Desktop con WSL 2 en Windows.

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

## Instalación sin Docker (SQLite)

El proyecto está configurado por defecto para usar SQLite, así que no necesitas instalar ni configurar MySQL para probarlo localmente.

```bash
git clone https://github.com/LeandroRubio-73456/elicrochet-ecommerce.git
cd elicrochet-ecommerce
composer run setup
```

`composer run setup` instala dependencias de Composer y npm, copia `.env.example` a `.env`, genera la clave de la aplicación, corre las migraciones con seeders y compila los assets.

Para levantar el servidor, la cola y Vite en un solo comando:

```bash
composer run dev
```

Accede a `http://localhost:8000`.

*(Si prefieres MySQL/MariaDB en vez de SQLite: configura las credenciales de `DB_*` en `.env` y ejecuta `php artisan migrate --seed` manualmente).*

## Datos de demostración

`composer run setup` (o `migrate --seed` en general) crea estas cuentas de prueba:

```text
Administrador: admin@elicrochet.com / password
Cliente:       test@example.com / password
```

## Pasarela de pago simulada

El checkout usa PayPhone. Si no hay credenciales reales de comercio configuradas (`PAYPHONE_TOKEN` vacío, el caso por defecto en `.env.example`), el sistema simula la pasarela automáticamente: en vez de redirigir a PayPhone, muestra una pantalla propia de "Modo simulación" con botones para aprobar o rechazar el pago. El resto del flujo (creación de orden, control de stock, correos) se ejecuta exactamente igual que con un pago real.

Esto se controla con la variable `PAYPHONE_SIMULATE` en `.env`:

```env
PAYPHONE_SIMULATE=true   # fuerza el modo simulado
PAYPHONE_SIMULATE=false  # fuerza llamadas reales a la API de PayPhone
# (vacío = automático: simula solo si PAYPHONE_TOKEN está vacío)
```

## Correo

Con Sail, los correos se capturan en Mailpit (`http://localhost:8025`), sin necesidad de configurar ningún proveedor externo. Sin Docker, cambia `MAIL_MAILER=log` en `.env` para revisar los correos en `storage/logs/laravel.log`.

## Pruebas y calidad de código

```bash
php artisan test
vendor/bin/pint --test
```

Con Sail:

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
```

El CI corre ambos comandos en cada push/PR, además de un análisis estático con SonarCloud (calidad de código y cobertura, visibles en los badges de arriba). La cobertura actual de la suite de pruebas es de aproximadamente 81%.

## Solución rápida de problemas

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan storage:link
```

Si aparece un error de permisos en Docker, ejecuta los comandos Artisan dentro del contenedor mediante Sail y verifica que la carpeta del proyecto tenga permisos de escritura para `storage` y `bootstrap/cache`.

## Seguridad

Las credenciales de los seeders son únicamente para demostración. En una instalación real cambia las contraseñas, configura `APP_DEBUG=false`, usa HTTPS, define credenciales reales de PayPhone y un correo SMTP seguro.

## Licencia

Este proyecto es software de código abierto bajo la licencia [MIT](https://opensource.org/licenses/MIT).
