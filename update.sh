#!/bin/bash

set -e

#docker system prune -f

# pull latest version of stacks repository itself
git pull
git checkout master

# pull latest version of website repository
cd ./dockerfiles/php/var/www/html
git reset --hard
git pull
git checkout master
cd ../../../../../

docker-compose build

docker-compose up -d

docker-compose exec php php artisan migrate --force
docker-compose exec php php artisan opcache:clear
docker-compose exec php php artisan config:cache
docker-compose exec php php artisan event:cache
docker-compose exec php php artisan route:cache
docker-compose exec php php artisan view:cache
docker-compose exec php php artisan opcache:compile
