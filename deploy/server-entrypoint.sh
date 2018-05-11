#!/bin/bash

echo "Composer install";
composer install -d /var/www/server

echo "Setting directory permissions";
chown -R www-data.www-data /var/www && chmod 775 /var/www;

echo "Create symlink for production builds";
ln -s /var/www/client/build /var/www/server/public/client

exec "$@"
