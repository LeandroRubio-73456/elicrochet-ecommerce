<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="EliCrochet Ecommerce">
  <br>
  <a href="https://github.com/LeandroRubio-73456/EliCrochet-Ecommerce/actions/workflows/ci.yml"><img src="https://github.com/LeandroRubio-73456/EliCrochet-Ecommerce/actions/workflows/ci.yml/badge.svg" alt="CI Quality Gate EliCrochet"></a>
  <a href="https://sonarcloud.io/summary/new_code?id=LeandroRubio-73456_elicrochet-ecommerce"><img src="https://sonarcloud.io/api/project_badges/measure?project=LeandroRubio-73456_elicrochet-ecommerce&metric=alert_status" alt="Quality Gate Status"></a>
  <a href="https://sonarcloud.io/summary/new_code?id=LeandroRubio-73456_elicrochet-ecommerce"><img src="https://sonarcloud.io/api/project_badges/measure?project=LeandroRubio-73456_elicrochet-ecommerce&metric=coverage" alt="Coverage"></a>
</p>

# EliCrochet Ecommerce

<<<<<<< Updated upstream
Este es un proyecto de comercio electrónico desarrollado con Laravel, diseñado para la venta y gestión de productos de crochet.
=======
<p align="center">
  <strong>Plataforma de comercio electrónico especializada en productos artesanales de crochet</strong>
</p>

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

---

## Tabla de Contenidos

- [Descripción](#-descripción)
- [Características Principales](#-características-principales)
- [Tecnologías](#-tecnologías)
- [Galería](#-galería)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Ejecución](#-ejecución)
- [Credenciales de Acceso](#-credenciales-de-acceso)
- [Tests y Calidad](#-tests-y-calidad)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Licencia](#-licencia)
- [Contacto](#-contacto)

---

## Descripción

**EliCrochet Ecommerce** es una plataforma de comercio electrónico desarrollada con **Laravel 12**, especializada en la venta y gestión de productos artesanales de crochet. El sistema ofrece una experiencia robusta y segura, respaldada por un índice de **cobertura de pruebas superior al 80%**.

- Carrito de compras reactivo y seguro
- Pasarela de pagos integrada (**PayPhone**)
- Gestión avanzada de pedidos (Stock y Personalizados)
- Sistema dinámico de perfiles y direcciones (con soporte para cédula/ID)
- Panel administrativo con control de ventas
- Calidad de código certificada con **SonarCloud** y **Lighthouse CI**

---

## Características Principales

### Para Clientes
- Catálogo optimizado con filtros dinámicos y búsqueda.
- Carrito de compras con persistencia y validaciones de stock en tiempo real.
- **Pedidos Personalizados:** Formulario especializado para solicitudes a medida con carga de referencias.
- Gestión de perfil completa y libreta de direcciones detallada.
- Historial de pedidos con seguimiento de estados y reintentos de pago.
- Sistema de valoraciones y testimonios.

### Para Administradores (Back-office)
- **Panel de Control:** Resumen de ventas, gestión de inventario y alertas de stock bajo.
- Gestión de catálogo con carga múltiple de imágenes.
- Administración de usuarios con roles definidos.
- Sistema de estados de pedido (Pendiente, Pagado, Fabricando, Enviado, Completado).
- **Gestión de Ventas:** Visualización detallada de transacciones y exportación a Excel.
- Generación de etiquetas de envío.

---

## Tecnologías

### Backend & Core
- **Framework:** Laravel 12.x
- **PHP:** 8.2+
- **Base de Datos:** MySQL / SQLite (optimizado para testing en memoria)
- **Pagos:** PayPhone API Integration
- **Estándar:** PSR-12 / Laravel Pint

### Frontend
- **Styles:** CSS Moderno (Vanilla CSS) para la interfaz de cliente.
- **Admin Template:** Basado en Bootstrap 5 para el panel administrativo.
- **JavaScript:** Vanilla JS
- **Build Tool:** Vite 7.x
- **Icons:** Tabler Icons

### Calidad & Testing
- **PHPUnit:** +100 tests unitarios e integración (+80% coverage)
- **SonarCloud:** Análisis estático detallado y Quality Gate
- **GitHub Actions:** Pipeline de CI/CD automatizado
- **Lighthouse CI:** Monitorización continua de WPO, SEO y Accesibilidad

---
>>>>>>> Stashed changes

## Requisitos

Asegúrate de tener instalados los siguientes componentes en tu entorno de desarrollo:

<<<<<<< Updated upstream
- [PHP 8.2](https://www.php.net/) o superior
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) y NPM
=======
| Software | Versión Mínima | Enlace |
|:---------|:---------------|:-------|
| PHP | 8.2 | [Descargar](https://www.php.net/) |
| Composer | 2.x | [Descargar](https://getcomposer.org/) |
| Node.js | 20.x | [Descargar](https://nodejs.org/) |
| MySQL | 8.0 | [Descargar](https://www.mysql.com/) |

---
>>>>>>> Stashed changes

## Instalación y Configuración

<<<<<<< Updated upstream
Sigue estos pasos para configurar el proyecto en tu máquina local:

1.  **Clonar el repositorio**
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd EliCrochet-Ecommerce
    ```

2.  **Ejecutar el script de configuración**
    El proyecto incluye un comando personalizado de Composer que automatiza la instalación de dependencias, la configuración del archivo `.env`, la generación de la clave de la aplicación, las migraciones de base de datos y la construcción de los assets del frontend.

    ```bash
    composer run setup
    ```

    > **Nota:** Este comando ejecutará internamente:
    > - `composer install`
    > - Copia de `.env.example` a `.env` (si no existe)
    > - `php artisan key:generate`
    > - `php artisan migrate --force`
    > - `npm install`
    > - `npm run build`
=======
### Configuración Rápida (Recomendado)

```bash
# 1. Clonar el repositorio
git clone https://github.com/LeandroRubio-73456/elicrochet-ecommerce.git
cd elicrochet-ecommerce

# 2. Instalación automatizada
composer run setup
```

El script `setup` preparará automáticamente:
- Dependencias PHP y Node.js.
- Variables de entorno (`.env`).
- Claves de cifrado y base de datos (Migraciones + Seeders).
- Compilación final de assets.

---
>>>>>>> Stashed changes

## Desarrollo y Ejecución

<<<<<<< Updated upstream
Para iniciar el servidor de desarrollo y los procesos necesarios (vite, queue, etc.), utiliza el siguiente comando que ejecuta todo en paralelo:
=======
Para iniciar el ecosistema de desarrollo completo:
>>>>>>> Stashed changes

```bash
composer run dev
```

<<<<<<< Updated upstream
O si prefieres ejecutar solo el servidor de Laravel:

```bash
php artisan serve
```
=======
Este comando inicia de forma concurrente:
- Servidor Laravel (`serve`).
- Servidor de assets Vite (`dev`).
- Listener de colas para notificaciones y procesos en segundo plano.
>>>>>>> Stashed changes

## Tests

<<<<<<< Updated upstream
Para ejecutar las pruebas automatizadas del proyecto:

```bash
php artisan test
```
=======
## Tests y Calidad de Código

El proyecto prioriza la estabilidad. Para ejecutar la suite de pruebas:

```bash
# Ejecutar tests estándar
composer run test

# Ejecutar auditoría de estilos
composer run lint
```

### Reportes de Calidad
- **Cobertura:** El proyecto mantiene un _Quality Gate_ estricto que exige >80% de cobertura en archivos críticos.
- **Performance:** Auditorías de Lighthouse integradas para mantener LCP < 2.5s y CLS < 0.1.

---

## Estructura del Proyecto

```
elicrochet-ecommerce/
├── app/
│   ├── Http/Controllers/      # Lógica de flujo (Front/Admin/Customer)
│   ├── Models/                # Modelos con Eloquent ORM
│   ├── Services/              # Servicios de negocio (Cart, Payments)
│   └── Mail/                  # Notificaciones transaccionales
├── database/                  # Migraciones, Factories y Seeders
├── resources/
│   ├── css/                   # TailwindCSS 4 & Modern CSS
│   └── views/                 # Blade Templates (Layouts modulares)
├── routes/                    # Definición de rutas (web.php)
└── tests/                     # Suite de Unit/Feature Testing
```

---

## Licencia

Este proyecto se distribuye bajo la Licencia MIT.

---

## Contacto

**Leandro Rubio**
- GitHub: [@LeandroRubio-73456](https://github.com/LeandroRubio-73456)
- Proyecto: [EliCrochet Ecommerce](https://github.com/LeandroRubio-73456/elicrochet-ecommerce)

<p align="center">
  <sub>Lanzamiento v1.0.0</sub>
</p>
>>>>>>> Stashed changes
