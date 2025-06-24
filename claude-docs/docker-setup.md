# Docker Setup para Admin Genérico

Este documento explica cómo funciona la configuración de Docker para el proyecto Admin Genérico y cómo usarla para levantar la aplicación desde un repositorio clonado.

## Arquitectura del Sistema

El proyecto utiliza una arquitectura de microservicios con Docker Compose que incluye:

- **Laravel App**: Aplicación principal corriendo en contenedor PHP/Laravel
- **MySQL**: Base de datos MySQL 8.0
- **Volúmenes persistentes**: Para datos de la base de datos y storage de Laravel
- **Red interna**: Para comunicación entre contenedores

## Archivos de Configuración

### 1. docker-compose.yml

Define la orquestación de servicios:

```yaml
services:
  laravel:
    build: .
    user: "0:0"
    container_name: admin-generico-app
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=admin_generico
      - DB_USERNAME=laravel_user
      - DB_PASSWORD=laravel_password
    depends_on:
      - mysql

  mysql:
    image: mysql:8.0
    container_name: admin-generico-mysql
    environment:
      - MYSQL_ROOT_PASSWORD=root_password
      - MYSQL_DATABASE=admin_generico
      - MYSQL_USER=laravel_user
      - MYSQL_PASSWORD=laravel_password
```

**Características clave:**
- Expone el puerto 8000 para acceso web
- Configura variables de entorno para la base de datos
- Monta volúmenes para persistencia de datos
- Excluye `node_modules` y `vendor` para optimización

### 2. Dockerfile

Construye la imagen de la aplicación Laravel:

```dockerfile
FROM bitnami/laravel:latest

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y default-mysql-client curl

# Instala Node.js 20.x para el frontend
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Copia y configura el script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
```

**Características clave:**
- Usa imagen base Bitnami Laravel optimizada
- Instala MySQL client para conectividad
- Instala Node.js 20.x para compilar assets
- Configura script de entrada personalizado

### 3. entrypoint.sh

Script de inicialización que ejecuta los siguientes pasos:

1. **Configuración inicial**:
   - Copia `.env.example` a `.env` si no existe
   - Configura variables de base de datos

2. **Preparación del entorno**:
   - Crea directorios de storage y cache
   - Configura permisos correctos

3. **Instalación de dependencias**:
   - `composer install` (PHP)
   - `npm install && npm run build` (Frontend)

4. **Configuración de Laravel**:
   - Genera clave de aplicación
   - Espera a que MySQL esté disponible
   - Ejecuta migraciones
   - Ejecuta seeders
   - Crea symlink para storage público

5. **Inicio del servidor**:
   - Inicia servidor de desarrollo en puerto 8000

## Cómo Usar la Configuración

### Prerequisitos

- Docker y Docker Compose instalados
- Git para clonar el repositorio

### Pasos para Levantar la Aplicación

1. **Clonar el repositorio**:
   ```bash
   git clone <url-del-repositorio>
   cd admin-generico
   ```

2. **Levantar los servicios**:
   ```bash
   docker-compose up --build
   ```

3. **Acceder a la aplicación**:
   - URL: http://localhost:8000
   - La aplicación estará disponible una vez completada la inicialización

### Comandos Útiles

- **Construir y levantar en background**:
  ```bash
  docker-compose up --build -d
  ```

- **Ver logs en tiempo real**:
  ```bash
  docker-compose logs -f laravel
  ```

- **Ejecutar comandos Artisan**:
  ```bash
  docker-compose exec laravel php artisan <comando>
  ```

- **Acceder al contenedor**:
  ```bash
  docker-compose exec laravel bash
  ```

- **Detener servicios**:
  ```bash
  docker-compose down
  ```

- **Limpiar volúmenes (reinicio completo)**:
  ```bash
  docker-compose down -v
  ```

## Flujo de Inicialización

1. Docker Compose inicia el contenedor MySQL
2. Se construye la imagen de Laravel usando el Dockerfile
3. El contenedor Laravel inicia y ejecuta `entrypoint.sh`
4. El script espera a que MySQL esté disponible
5. Se ejecutan migraciones y seeders
6. Se inicia el servidor Laravel en puerto 8000

## Persistencia de Datos

- **mysql_data**: Almacena los datos de la base de datos MySQL
- **laravel_storage**: Almacena archivos subidos y logs de Laravel
- **Código fuente**: Se monta como volumen para desarrollo

## Configuración Inicial Después del Primer Levantamiento

Después de levantar la aplicación por primera vez, ejecuta estos comandos dentro del contenedor:

```bash
# Acceder al contenedor
docker-compose exec laravel bash

# Ejecutar las migraciones de permisos
php artisan migrate

# (Opcional) Crear algunos roles y permisos básicos
php artisan tinker
```

En tinker puedes crear roles básicos:
```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Crear permisos básicos (especificar guard 'web')
Permission::create(['name' => 'create_users', 'guard_name' => 'web']);
Permission::create(['name' => 'edit_users', 'guard_name' => 'web']);
Permission::create(['name' => 'delete_users', 'guard_name' => 'web']);
Permission::create(['name' => 'view_users', 'guard_name' => 'web']);

// Crear roles básicos (especificar guard 'web')
$admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
$user = Role::create(['name' => 'user', 'guard_name' => 'web']);

// Asignar todos los permisos del guard 'web' al admin
$admin->givePermissionTo(Permission::where('guard_name', 'web')->get());
```

## Troubleshooting

- Si la aplicación no inicia, verificar los logs: `docker-compose logs laravel`
- Si hay problemas de permisos, revisar la configuración de usuario en docker-compose.yml
- Para reiniciar completamente: `docker-compose down -v && docker-compose up --build`
- Si hay errores de "Trait not found", ejecutar `composer install` dentro del contenedor