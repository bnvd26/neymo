echo '1/4'
docker-compose up -d
echo '2/4'
docker container exec app composer install
echo '3/4'
docker container exec app php bin/console d:s:u --force
echo '4/4'
docker container exec app php bin/console d:f:l --no-interaction

