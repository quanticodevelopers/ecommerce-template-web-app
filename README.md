# Ecommerce Template Web App

## Desarrollador

Alan Alfredo Bernal Espinoza

## Descripción

Plantilla de aplicación web orientada a e-commerce construida sobre Laravel. Este proyecto sirve como punto de partida (starter kit) que integra Inertia.js con React para renderizado del frontend, autenticación nativa con guards separados para clientes y administradores, y herramientas modernas de desarrollo y testing.

## Instrucciones de instalación y ejecución (recomendado: Herd)

Sugerencia: usa Laravel Herd para gestionar PHP, servicios y nombres de sitio locales. Pasos básicos desde la raíz del proyecto (PowerShell):

```powershell
# Inicia los servicios necesarios con Herd (ejemplo)
herd services:start mysql redis

# Instala dependencias PHP y JS
composer install --no-interaction
pnpm install

# Crea el .env a partir del ejemplo y genera la clave de la app
Copy-Item -Path .env.example -Destination .env -Force
php artisan key:generate

# Ejecuta migraciones (si corresponde)
php artisan migrate --force

# Levanta el entorno de desarrollo (usa vite / servidor de Laravel vía los scripts del proyecto)
pnpm run dev
```

También puedes usar los scripts definidos en `composer.json` para configurar y arrancar todo de forma automática:

```powershell
composer run setup
composer run dev
```

Para ejecutar tests:

```powershell
php artisan test --compact
```

Para construir assets para producción:

```powershell
pnpm run build
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Tecnologías usadas

- PHP 8.4
- Laravel Framework v13
- Inertia Laravel (inertiajs/inertia-laravel) v3
- React v19
- @inertiajs/react v3
- Tailwind CSS v4
- Autenticación nativa de Laravel con modelos y guards separados
- Laravel Wayfinder (generación de helpers de rutas/acciones)
- Laravel Boost (herramientas del proyecto)
- Vite (bundling)
- pnpm (gestor de paquetes JS)
- Pest (testing) v4
- PHPUnit v12
- Laravel Herd (entorno de desarrollo local)
