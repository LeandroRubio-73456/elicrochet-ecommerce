# EliCrochet Ecommerce

<p align="center">
  <img src="public/assets/images/Logo.webp" alt="EliCrochet Logo" width="200">
</p>

Solución E-commerce desarrollada para EliCrochet, una microempresa de artesanías en Ecuador. Este sistema optimizó su gestión de inventario y permitió digitalizar sus ventas que antes eran manuales por WhatsApp.

## [Ver Demo en Vivo](https://elicrochet.shop)

<p align="center">
  <a href="https://github.com/LeandroRubio-73456/EliCrochet-Ecommerce/actions/workflows/ci.yml">
    <img src="https://github.com/LeandroRubio-73456/EliCrochet-Ecommerce/actions/workflows/ci.yml/badge.svg" alt="CI Quality Gate">
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

## Stack Tecnológico

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
</p>

## Requisitos Previos

- **PHP 8.2+** (con extensiones estándar de Laravel habilitadas)
- **Composer 2.x**
- **Node.js 20.x**

> **Nota:** El proyecto está configurado por defecto para usar **SQLite**, por lo que no necesitas instalar MySQL ni crear bases de datos manualmente para probarlo.

## Instalación Rápida

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/LeandroRubio-73456/elicrochet-ecommerce.git
   cd elicrochet-ecommerce
   ```

2. **Configurar automágicamente:**
   El proyecto incluye un script que instala dependencias, configura el entorno (.env), genera claves, base de datos y compila los assets.
   ```bash
   composer run setup
   ```

   *(Si prefieres usar MySQL/MariaDB: Copia el `.env.example` a `.env`, configura tus credenciales de BD y ejecuta `composer install && php artisan migrate --seed` manualmente).*

## Ejecución

Para iniciar el servidor local, los workers y Vite en un solo comando:

```bash
composer run dev
```

Accede a la aplicación en: [http://localhost:8000](http://localhost:8000)

## Credenciales de Prueba

El comando de setup genera automáticamente estos usuarios para que puedas probar todos los roles:

| Rol | Email | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@elicrochet.com` | `password` |
| **Cliente** | `test@example.com` | `password` |

## Tests

El proyecto cuenta con una suite de pruebas automatizadas (>80% de cobertura) para garantizar la estabilidad.

```bash
# Ejecutar todos los tests
php artisan test
```

## Galería

<p align="center">
  <img src="public/screenshots/home.png" alt="Home Page" width="45%">
  <img src="public/screenshots/admin.png" alt="Admin Dashboard" width="45%">
</p>

## Licencia

Este proyecto es software de código abierto bajo la licencia [MIT](https://opensource.org/licenses/MIT).
