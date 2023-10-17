SHELL = /bin/sh

APP_CONTAINER_NAME := app
MASTER_TYPE := main
PROJECT_FOLDER := /home/deployer/ORCHESTRATOR
PROJECT_FOLDER := ORCHESTRATOR

docker_bin := $(shell command -v docker 2> /dev/null)
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)
git_bin := $(shell command -v git 2> /dev/null)

.SILENT:
.ONESHELL:
.PHONY : help \
		 up down restart shell install build init worker
.DEFAULT_GOAL := help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start all containers (in background) for development ##
	$(docker_compose_bin) up -d app webserver

down: ## Stop all started for development containers
	$(docker_compose_bin) down

restart: ## Restart all started for development containers
	$(docker_compose_bin) restart

shell: up ## Start shell into application container
	$(docker_compose_bin) exec "$(APP_CONTAINER_NAME)" bash

exec: ## Execute command Usage: make exec COMMAND="composer install"
	$(docker_compose_bin) exec "$(APP_CONTAINER_NAME)" $(COMMAND)

install: up ## Install application dependencies into application container
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "composer install --no-dev"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "npm ci"

init: install ## Make full application initialization (install, seed, build assets, etc)
	$(docker_compose_bin) exec "$(APP_CONTAINER_NAME)" bash -c "php artisan key:generate"
	$(docker_compose_bin) exec "$(APP_CONTAINER_NAME)" bash -c "php artisan migrate"
	$(docker_compose_bin) exec "$(APP_CONTAINER_NAME)" bash -c "php artisan optimize"

build: install ## Install new packages, run migration and optimize
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "composer dumpautoload"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "php artisan migrate --force"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "php artisan route:clear"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "php artisan optimize"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "npm run prod"
	$(docker_compose_bin) restart app

## Clear cache (undefined index + php class)
cache:
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "composer dumpautoload"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "php artisan optimize:clear"
	$(docker_compose_bin) exec -T "$(APP_CONTAINER_NAME)" bash -c "php system/clear-cache.php"
