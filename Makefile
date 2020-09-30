default: help

THIS_MAKEFILE_PATH = $(word 1,$(MAKEFILE_LIST))
PATH_ROOT      = $(shell cd $(dir $(THIS_MAKEFILE_PATH));pwd)
PATH_SRC       = $(PATH_ROOT)/src
PATH_VENDOR    = $(PATH_ROOT)/vendor
PATH_OAM       = $(PATH_ROOT)/oam
CMD_ENV        = /usr/bin/env
CMD_LS         = $(CMD_ENV) ls -lah
CMD_MKDIR      = $(CMD_ENV) mkdir -p
CMD_CHMOD      = $(CMD_ENV) chmod

CACHE_CLEAR = $(CMD_PHP) $(PATH_ROOT)/bin/console cache:clear $1
CMD_COMP = $(CMD_ENV) composer

include $(PATH_ROOT)/makedir/yarn.mk
include $(PATH_ROOT)/makedir/release.mk
include $(PATH_ROOT)/makedir/console.mk

.PHONY: dc-rebuild
dc-rebuild: dc-delete dc-build dc-up  ## delete old system and build new

.PHONY: dc-build
dc-build: ## build environment and initialize composer and project dependencies
		docker-compose build

.PHONY: dc-stop
dc-stop: ## stop environment
		docker-compose stop

.PHONY: dc-delete
dc-delete: ## stop and delete containers, clean volumes.
		docker-compose stop
		docker-compose rm -v -f

.PHONY :dc-up
dc-up: dc-only-up dc-folder-access

.PHONY :dc-only-up
dc-only-up: ## up environments, Run containers in the background, print new container names
		docker-compose up -d

.PHONY :dc-restart
dc-restart: dc-stop dc-up  ## stop and restart docker compose

.PHONY: sh
sh: ## gets inside a container, use 's' variable to select a service. make s=php sh
		docker-compose exec $(s) sh -l

.PHONY: dc-comp-up
dc-comp-up: ## Update project dependencies
		docker-compose run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer update'

.PHONY: dc-comp-inst-o
dc-comp-inst-o: ## Update project dependencies with optimization (use for prod only)
		docker-compose run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer install -o --apcu-autoloader --no-dev'

.PHONY: dc-comp-inst
dc-comp-inst: ## Update project dependencies
		docker-compose run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer install'

.PHONY: dc-cs-fix
dc-cs-fix: ## executes php cs fixer
		docker-compose run --rm php sh -lc './vendor/bin/php-cs-fixer --no-interaction -v fix'

.PHONY: dc-mysql-console
dc-mysql-console: ## Update project dependencies
		docker-compose run --rm mysql sh

.PHONY: dc-cc
dc-cc: dc-cc-portal dc-cc-admin## clear all symfony cache

.PHONY: dc-cc-portal
dc-cc-portal: ## clear symfony cache for portal
		docker-compose run --rm php sh -lc 'bin/console cache:clear'

.PHONY: dc-cc-admin
dc-cc-admin: ## clear symfony cache for admin
		docker-compose run --rm php sh -lc 'bin/admin_console cache:clear'

.PHONY: dc-migration-create
dc-migration-create: ## Create doctrine migration
		docker-compose run --rm php sh -lc 'bin/console make:migration'

.PHONY: dc-migration-run
dc-migration-run: ## Create doctrine migration
		docker-compose run --rm php sh -lc 'bin/console doctrine:migrations:migrate'


.PHONY: dc-tests
dc-tests: ## update test db execute project unit tests
		docker-compose exec php sh -lc './bin/console doctrine:schema:update  --force --env=test'
		docker-compose exec php sh -lc "./vendor/bin/phpunit $(conf)"

.PHONY: dc-db-update
dc-db-update: ## update dev db
		docker-compose exec php sh -lc './bin/console doctrine:schema:update  --force'

.PHONY: dc-lint-container
dc-lint-container: ## Checking the types of all service arguments whenever the container is compiled can hurt performance
		docker-compose exec php sh -lc './bin/console lint:container'

.PHONY: dc-folder-access
dc-folder-access: ## Checking the types of all service arguments whenever the container is compiled can hurt performance
		docker-compose run --rm php sh -lc 'sh oam/php/folder_access.sh & exit'

.PHONY: help
help: ## Display this help message
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
