#!/bin/sh

chmod -R 777 /var/www/project
chown -R www-data:www-data /var/www/project
chmod -R g+s /var/www/project

exec "$@"
