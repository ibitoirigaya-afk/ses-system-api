.PHONY: up down build restart logs ps shell db-shell migrate fresh seed test route cache-clear

COMPOSE=docker compose

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) up -d --build

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

shell:
	$(COMPOSE) exec api sh

db-shell:
	$(COMPOSE) exec postgres psql -U ses_user -d ses_system

migrate:
	$(COMPOSE) exec api php artisan migrate

fresh:
	$(COMPOSE) exec api php artisan migrate:fresh

seed:
	$(COMPOSE) exec api php artisan db:seed

test:
	$(COMPOSE) exec api php artisan test

route:
	$(COMPOSE) exec api php artisan route:list --path=api

cache-clear:
	$(COMPOSE) exec api php artisan optimize:clear