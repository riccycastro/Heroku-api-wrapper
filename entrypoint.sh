#!/bin/sh
cd /var/www/html/internations-app

if [ ! -d "./vendor" ]; then
  composer install --no-interaction
fi
