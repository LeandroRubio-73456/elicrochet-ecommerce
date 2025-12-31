<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="EliCrochet Ecommerce">
</p>

# EliCrochet Ecommerce

Este es un proyecto de comercio electrónico desarrollado con Laravel, diseñado para la venta y gestión de productos de crochet.

## Requisitos

Asegúrate de tener instalados los siguientes componentes en tu entorno de desarrollo:

- [PHP 8.2](https://www.php.net/) o superior
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) y NPM

## Instalación

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

## Ejecución

Para iniciar el servidor de desarrollo y los procesos necesarios (vite, queue, etc.), utiliza el siguiente comando que ejecuta todo en paralelo:

```bash
composer run dev
```

O si prefieres ejecutar solo el servidor de Laravel:

```bash
php artisan serve
```

## Tests

Para ejecutar las pruebas automatizadas del proyecto:

```bash
php artisan test
```
