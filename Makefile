COMPOSE=docker-compose

.PHONY: up down build shell install console logs ps

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

shell:
	$(COMPOSE) exec php sh

install:
	$(COMPOSE) exec php composer install

console:
	$(COMPOSE) exec php php bin/console

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps
