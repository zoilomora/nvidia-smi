UID=$(shell id -u)
GID=$(shell id -g)
DOCKER_PHP_SERVICE=php-apache

erase:
		docker-compose down -v

pull:
		docker-compose pull

start:
		docker-compose up -d

stop:
		docker-compose stop

bash:
		docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh

logs:
		docker-compose logs -f ${DOCKER_PHP_SERVICE}
