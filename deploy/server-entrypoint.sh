#!/bin/bash

until pg_isready -h ${DB_HOST} -p ${DB_PORT} -d ${DB_DATABASE}; do
    echo "$(date) - waiting for postgres database ${DB_DATABASE} on ${DB_HOST}:${DB_PORT}..."
    sleep 1
done
echo "Postgres is ready"

echo "update new relic config"
sed -i \
        -e "s/newrelic.license =.*/newrelic.license = ${NR_INSTALL_KEY}/" \
        -e "s/newrelic.appname =.*/newrelic.appname = ${APP_NAME}/" \
        /usr/local/etc/php/conf.d/newrelic.ini

echo "Composer install";
composer install -d /var/www/server

echo "Setting directory permissions";
chown -R www-data.www-data /var/www && chmod 775 /var/www;

echo "Run migrations";
php server/artisan migrate --force

exec "$@"
