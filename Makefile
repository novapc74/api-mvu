init: create_network docker-down docker-pull docker-build docker-up

create_network:
	@if [ -z "$$(docker network ls --filter name=mvu-server -q)" ]; then \
		docker network create mvu-server; \
	else \
		echo "Docker network mvu-server already exists, skipping creation."; \
	fi

create_shared_network:
	docker network create --driver bridge shared-network

docker-down:
	docker compose --env-file ./project/.env.local down --remove-orphans

docker-pull:
	docker compose --env-file ./project/.env.local pull

docker-build:
	docker compose --env-file ./project/.env.local build --pull

docker-up: create_network
	docker compose --env-file ./project/.env.local up -d

php-cli:
	docker compose --env-file ./project/.env.local run --rm php-cli bash

dev-update:
	docker compose --env-file ./project/.env.local exec php-cli bash
	composer install
	bin/console d:m:m --no-inreraction

yarn-watch:
	docker compose --env-file ./project/.env.local run --rm node-cli yarn watch