.PHONY: install up down build shell migrate fresh seed test route cache-clear

install:
	composer install

up:
	docker compose up

down:
	docker compose down

build:
	docker compose up --build

shell:
	docker compose exec app bash

migrate:
	docker compose exec app php artisan migrate

fresh:
	docker compose exec app php artisan migrate:fresh

seed:
	docker compose exec app php artisan db:seed

test:
	docker compose exec app php artisan test

route:
	docker compose exec app php artisan route:list

cache-clear:
	docker compose exec app php artisan optimize:clear