<p align="center">
  <img src="public/assets/images/Logo.webp" width="15%" alt="EliCrochet Logo">
</p>

<h1 align="center">EliCrochet Ecommerce</h1>

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

## 📖 Tabla de Contenidos

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

## 📝 Descripción

**EliCrochet Ecommerce** es una plataforma de comercio electrónico desarrollada con **Laravel 12**, especializada en la venta y gestión de productos artesanales de crochet. El sistema ofrece una experiencia completa tanto para clientes como para administradores, incluyendo:

- Carrito de compras interactivo
- Gestión completa de pedidos
- Solicitudes de pedidos personalizados
- Sistema de autenticación y perfiles de usuario
- Panel administrativo completo con estadísticas
- Integración con herramientas de análisis de calidad de código

---

## Características Principales

### Para Clientes
- Catálogo de productos con filtros y búsqueda
- Carrito de compras con gestión en tiempo real
- Solicitud de productos personalizados
- Diseño responsive y optimizado para móviles
- Gestión de perfil y direcciones
- Historial de pedidos
- Sistema de valoraciones y reseñas

### Para Administradores
- Dashboard con estadísticas en tiempo real
- Gestión completa de productos y categorías
- Carga múltiple de imágenes
- Administración de usuarios y roles
- Gestión de pedidos y estados
- Visualización de pedidos personalizados
- Reportes y análisis de ventas

---

## Tecnologías

<p align="left">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

### Backend
- **Framework:** Laravel 12.x
- **PHP:** 8.2+
- **Base de Datos:** MySQL/MariaDB / SQLite (Dev)
- **Queue:** Redis / Database

### Frontend
- **CSS Framework:** Bootstrap 5
- **JavaScript:** Vanilla JS + Alpine.js
- **Build Tool:** Vite
- **Icons:** Tabler Icons

### Testing & Quality
- **PHPUnit:** Tests unitarios e integración
- **SonarCloud:** Análisis estático de código
- **GitHub Actions:** CI/CD Pipeline

---

## Galería

| Vista Cliente | Vista Administración |
|:---:|:---:|
| <img src="public/screenshots/home.jpg" width="400" alt="Home"> | <img src="public/screenshots/admin.jpg" width="400" alt="Admin Dashboard"> |

---

## Requisitos

Antes de comenzar, asegúrate de tener instalado:

| Software | Versión Mínima | Enlace |
|:---------|:---------------|:-------|
| PHP | 8.2 | [Descargar](https://www.php.net/) |
| Composer | 2.x | [Descargar](https://getcomposer.org/) |
| Node.js | 18.x | [Descargar](https://nodejs.org/) |
| MySQL/MariaDB | 8.0/10.x | [Descargar](https://www.mysql.com/) |

### Extensiones PHP Requeridas
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

---

## Instalación

### Opción 1: Instalación Automática (Recomendado)

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/LeandroRubio-73456/elicrochet-ecommerce.git
   cd elicrochet-ecommerce
   ```

2. **Ejecutar script de configuración**
   ```bash
   composer run setup
   ```

   Este comando ejecuta automáticamente:
   - Instalación de dependencias PHP (`composer install`)
   - Copia del archivo `.env.example` a `.env`
   - Generación de key de aplicación (`php artisan key:generate`)
   - Ejecución de migraciones (`php artisan migrate`)
   - Seeders de datos de prueba (`php artisan db:seed`)
   - Instalación de dependencias Node (`npm install`)
   - Build de assets (`npm run build`)

### Opción 2: Instalación Manual

Si prefieres controlar cada paso:

```bash
# 1. Clonar repositorio
git clone https://github.com/LeandroRubio-73456/elicrochet-ecommerce.git
cd elicrochet-ecommerce

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_DATABASE=elicrochet
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Instalar dependencias frontend
npm install
npm run build

# 7. Crear enlace simbólico para storage
php artisan storage:link
```

---

## Ejecución

### Entorno de Desarrollo

**Opción 1: Comando único (recomendado)**
```bash
composer run dev
```

Este comando inicia automáticamente:
- Servidor de desarrollo Laravel (`php artisan serve`)
- Servidor Vite para hot-reload (`npm run dev`)
- Cola de trabajos (`php artisan queue:work`)

**Opción 2: Comandos individuales**

En terminales separadas, ejecuta:

```bash
# Terminal 1 - Servidor Laravel
php artisan serve

# Terminal 2 - Vite (desarrollo)
npm run dev

# Terminal 3 - Cola de trabajos (si usas queues)
php artisan queue:work
```

---

## Credenciales de Acceso

Para propósitos de desarrollo y prueba, se incluyen usuarios precargados:

| Rol | Email | Contraseña | Permisos |
|:----|:------|:-----------|:---------|
| **Administrador** | `admin@elicrochet.com` | `password` | Acceso completo al sistema |
| **Cliente** | `cliente@elicrochet.com` | `password` | Compras y gestión de perfil |

---

## Tests y Calidad

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Con cobertura
php artisan test --coverage

# Tests específicos
php artisan test --filter NombreDelTest

# Tests con output detallado
php artisan test --verbose
```

### Análisis de Código

El proyecto está integrado con **SonarCloud** para garantizar la calidad del código:

- Análisis estático de código
- Cobertura de tests
- Detección de code smells
- Detección de vulnerabilidades
- Métricas de mantenibilidad

Ver reportes en: [SonarCloud Dashboard](https://sonarcloud.io/summary/new_code?id=LeandroRubio-73456_elicrochet-ecommerce)

### CI/CD Pipeline

Cada push y pull request ejecuta automáticamente:
1. Tests unitarios
2. Tests de integración
3. Análisis de código estático
4. Generación de reportes de cobertura

---

## Estructura del Proyecto

```
elicrochet-ecommerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controladores
│   │   └── Middleware/        # Middleware personalizado
│   ├── Models/                # Modelos Eloquent
│   └── Services/              # Lógica de negocio
├── database/
│   ├── migrations/            # Migraciones de BD
│   └── seeders/               # Seeders de datos
├── public/
│   ├── assets/                # Assets públicos
│   └── screenshots/           # Capturas de pantalla
├── resources/
│   ├── views/                 # Vistas Blade
│   ├── css/                   # Estilos
│   └── js/                    # JavaScript
├── routes/
│   ├── web.php               # Rutas web
│   └── api.php               # Rutas API
└── tests/                     # Tests automatizados
```

---

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

---

## Contacto

**Leandro Rubio**
- GitHub: [@LeandroRubio-73456](https://github.com/LeandroRubio-73456)
- Email: leandro.rubio@example.com
- LinkedIn: [Tu perfil](https://linkedin.com/in/leandrorubio)

**Link del Proyecto:** [https://github.com/LeandroRubio-73456/elicrochet-ecommerce](https://github.com/LeandroRubio-73456/elicrochet-ecommerce)

---

<p align="center">
  Hecho por <strong>Leandro Rubio</strong>
</p>

<p align="center">
  <sub>Proyecto de Tesis</sub>
</p>
