echo '1/5'
docker-compose up -d
echo '2/5'
docker container exec app composer install
echo '3/5'
docker container exec app bin/console d:d:c
echo '4/5'
docker container exec app php bin/console d:s:u --force
echo '5/5'
docker container exec app php bin/console d:f:l --no-interaction

