#!/bin/bash

# Step 1: Copy .env if it doesn't exist and set DB variables
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i 's/DB_HOST=.*/DB_HOST=db/' .env
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel/' .env
    sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel/' .env
    sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env
fi

# Step 2: Prepare storage and cache directories
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage
chmod -R 775 public/storage
chmod -R 777 storage bootstrap/cache

# Step 3: Install PHP and Node dependencies
composer install --no-interaction --no-progress
npm install
npm run build

# Step 3.1: Install PHP Redis extension for cache support
install_packages php-redis

# Step 4: Generate Laravel app key
php artisan key:generate --force

until mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" "$DB_DATABASE" > /dev/null 2>&1; do
  echo "Intentando conectar a $DB_HOST:$DB_PORT con usuario $DB_USERNAME a la base $DB_DATABASE"
  echo "Esperando a que la base de datos esté lista..."
  sleep 3
done

# Step 5: Run database migrations
php artisan migrate --force

# Step 5.1: Run database seeders
php artisan db:seed --force

# Step 5.2: Create storage symlink for public access to uploaded files
if [ ! -L public/storage ]; then
    rm -rf public/storage
    ln -s ../storage/app/public public/storage
fi

# Step 6: Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000