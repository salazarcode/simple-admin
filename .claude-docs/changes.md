# Admin Genérico - Pasos de Creación del Sistema

Este documento contiene los pasos realizados para crear el sistema Admin Genérico con Laravel, Livewire, Jetstream y Docker.

## 1. Instalación de Composer

```bash
# Instalar Composer localmente
curl -sS https://getcomposer.org/installer | php -- --install-dir=/home/andres/.local/bin --filename=composer
export PATH=$PATH:/home/andres/.local/bin
```

## 2. Instalación de Extensiones PHP Requeridas

```bash
# Instalar extensiones PHP necesarias para Laravel
sudo apt update
sudo apt install -y php-xml php-dom php-mbstring php-curl php-pdo php-mysql php-bcmath php-gd php-zip php-intl
```

## 3. Creación del Proyecto Laravel

```bash
# Crear proyecto Laravel en directorio actual
composer create-project laravel/laravel .
```

## 4. Configuración de Docker

Se creó el archivo `docker-compose.yml` con:
- **Laravel App**: Imagen `bitnami/laravel:latest` en puerto 8000
- **MySQL Database**: Imagen `mysql:8.0` en puerto 3306
- **Configuración de red**: Red privada `admin-generico-network`
- **Volúmenes**: Persistencia de datos MySQL

### Variables de entorno configuradas:
- DB_DATABASE: admin_generico
- DB_USERNAME: laravel_user
- DB_PASSWORD: laravel_password

## 5. Instalación de Livewire

```bash
# Instalar Livewire para componentes reactivos
composer require livewire/livewire
```

## 6. Instalación de Jetstream

```bash
# Instalar Laravel Jetstream
composer require laravel/jetstream

# Instalar scaffolding con Livewire (sin teams)
php artisan jetstream:install livewire
```

### Características instaladas:
- Autenticación completa (login, registro, password reset)
- Gestión de perfil de usuario
- Autenticación de dos factores
- Gestión de sesiones del navegador
- API tokens con Laravel Sanctum
- Interface con Livewire (sin teams)

## 7. Configuración de la Base de Datos

Se configuró el archivo `.env` para usar MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=admin_generico
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password
```

## 8. Comandos para Ejecutar el Proyecto

### Usando Docker:
```bash
# Levantar los contenedores
docker-compose up -d

# Ejecutar migraciones dentro del contenedor
docker-compose exec laravel php artisan migrate

# Crear usuario administrador (opcional)
docker-compose exec laravel php artisan tinker
```

### Desarrollo local:
```bash
# Instalar dependencias de Node.js
npm install

# Compilar assets para desarrollo
npm run dev

# Ejecutar migraciones
php artisan migrate

# Servir la aplicación
php artisan serve
```

## 9. Estructura del Proyecto

- **Framework**: Laravel 12.x
- **Frontend**: Livewire + Tailwind CSS
- **Autenticación**: Laravel Jetstream
- **Base de datos**: MySQL 8.0
- **Contenedores**: Docker con docker-compose

## 10. URLs Importantes

- **Aplicación**: http://localhost:8000
- **Login**: http://localhost:8000/login
- **Registro**: http://localhost:8000/register
- **Dashboard**: http://localhost:8000/dashboard
- **Perfil**: http://localhost:8000/user/profile

## Notas Adicionales

- El proyecto está configurado sin el sistema de teams de Jetstream
- Se incluye Laravel Sanctum para autenticación API
- Los assets están compilados y listos para producción
- La configuración de Docker permite desarrollo y despliegue fácil
- Se recomienda ejecutar `php artisan migrate` antes del primer uso