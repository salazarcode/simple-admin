# Admin Genérico

Sistema de administración genérico desarrollado con Laravel 12, diseñado para proporcionar una base sólida y escalable para aplicaciones web administrativas.

## Características

- 🚀 Laravel 12 con PHP 8.2+
- 🎨 Tailwind CSS para diseño moderno
- ⚡ Livewire para interactividad
- 🔐 Jetstream para autenticación y equipos
- 🗄️ MySQL como base de datos
- 🛠️ phpMyAdmin para administración de BD
- 🐳 Docker solo para servicios de infraestructura

## Configuración del Entorno de Desarrollo

### Prerrequisitos

**Instalar localmente:**
- [PHP 8.2+](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/)
- [Node.js 18+](https://nodejs.org/)
- [Docker](https://docs.docker.com/get-docker/) y Docker Compose
- Git

**Verificar instalación:**
```bash
php --version          # Debe ser 8.2+
composer --version
node --version          # Debe ser 18+
npm --version
docker --version
```

### Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone <repository-url>
   cd admin-generico
   ```

2. **Instalar dependencias:**
   ```bash
   composer install
   npm install
   ```

3. **Configurar variables de entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Levantar servicios Docker (solo MySQL + phpMyAdmin):**
   ```bash
   docker-compose up -d
   ```

5. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

### Desarrollo Diario

1. **Levantar servicios Docker:**
   ```bash
   docker-compose up -d
   ```

2. **En una terminal - Servidor Laravel:**
   ```bash
   php artisan serve
   ```

3. **En otra terminal - Assets (Vite):**
   ```bash
   npm run dev
   ```

### Acceso a Servicios

- **Aplicación**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
  - Usuario: `laravel_user`
  - Contraseña: `laravel_password`
- **MySQL**: localhost:3307

### Comandos Útiles

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver logs de Docker
docker-compose logs mysql

# Reiniciar servicios Docker
docker-compose restart
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
