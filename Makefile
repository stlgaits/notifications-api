DOCKER = docker
DOCKER_COMPOSE = $(DOCKER) compose
APP = $(DOCKER_COMPOSE) exec -it php
COMPOSER = $(APP) composer
PHP = $(APP) php

.DEFAULT_GOAL := help

##
## Setup
##-----------------------------------------------------------------

basic-install: ## Basic Install with local PHP & Symfony CLI
	$(DOCKER_COMPOSE) up -d
	composer install
	symfony console doctrine:migrations:migrate -n
	symfony console doctrine:fixtures:load -n --quiet

install: start ## Install with full Docker
	$(COMPOSER) install
	$(PHP) bin/console doctrine:migrations:migrate -n
	$(PHP) bin/console doctrine:fixtures:load -n --quiet


##
## Tests setup
## ---------------------------------------------------------------
test-install: ## Basic test Install with local PHP & Symfony CLI
	$(DOCKER_COMPOSE) up -d
	composer install
	symfony console doctrine:database:create --if-not-exists -n --env=test
	symfony console doctrine:migrations:migrate -n --env=test
	symfony console doctrine:fixtures:load -n --quiet --env=test

test:
	 php bin/phpunit

##
## Start/Stop
##-----------------------------------------------------------------

basic-start: ## Basic Start with local PHP & Symfony CLI
	$(DOCKER_COMPOSE) up -d
	symfony serve -d

start: ## Start with full Docker
	$(DOCKER_COMPOSE) --profile=full up -d

basic-stop: ## Basic Stop with local PHP & Symfony CLI
	$(DOCKER_COMPOSE) stop
	symfony server:stop

stop: ## Stop with full Docker
	$(DOCKER_COMPOSE) --profile=full stop

##
## Dev & QA
##-----------------------------------------------------------------

bash: ## Execute bash on the PHP Container
	$(APP) bash

fix:  rector_fix phpstan_fix ecs_fix  ## Fix everything

ecs_fix: ## Fix QA with coding standard
	vendor/bin/ecs check --fix

rector_fix: ## Fix QA with Rector
	vendor/bin/rector process

phpstan_fix: ## Fix QA with PHPStan
	vendor/bin/phpstan analyse src

##
## Utilities
##-----------------------------------------------------------------

.PHONY: help

help: ## Display all available commands on Make
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
