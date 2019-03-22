#!/bin/bash

until pg_isready -h ${DB_HOST} -p ${DB_PORT} -d ${DB_DATABASE}; do
    echo "$(date) - waiting for postgres database ${DB_DATABASE} on ${DB_HOST}:${DB_PORT}..."
    sleep 1
done
echo "Postgres is ready"

echo "Composer install";
composer install -d /var/www/server

echo "Setting directory permissions";
chown -R www-data.www-data /var/www && chmod 775 /var/www;

echo "Create symlink for production builds";
ln -s /var/www/client/build /var/www/server/public/client

echo "Run migrations";
php server/artisan migrate --force

exec "$@"
