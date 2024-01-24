COMPOSE_FILE := docker-compose.yml

all: docker-up-build back-install-dependencies back-cache-clear back-create-database back-run-migrations

docker-build:
	docker-compose -f $(COMPOSE_FILE) build

docker-up:
	docker-compose -f $(COMPOSE_FILE) up

docker-down:
	docker-compose -f $(COMPOSE_FILE) down

docker-up-build:
	docker-compose -f $(COMPOSE_FILE) up -d --build

back-install-dependencies:
	docker exec -it winners_api_php bash -c 'composer install'

back-cache-clear:
	docker exec -it winners_api_php bash -c './bin/console c:c'

back-create-database:
	docker exec -it winners_api_php bash -c './bin/console d:d:c'

back-run-migrations:
	docker exec -it winners_api_php bash -c './bin/console --no-interaction d:m:m'

.PHONY: build up down