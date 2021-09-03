#!/bin/bash

cd ./dockerfiles/php/var/www/html
git reset --hard
git checkout master
git pull
cd ../../../../../

docker-compose build
docker-compose up -d

docker-compose exec php php artisan migrate --force
docker-compose exec php php artisan opcache:compile
