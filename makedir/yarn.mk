
.PHONY: dc-yarn-install
dc-yarn-install: ## install all depebdencies for yarn
		docker-compose run --rm encore yarn install

.PHONY: dc-yarn-dev
dc-yarn-dev: ## update yarn for dev
		docker-compose run --rm encore yarn dev

.PHONY: dc-yarn-dev-watch
dc-yarn-dev-watch: ## update yarn for dev and enable watch
		docker-compose run --rm encore yarn dev --watch

.PHONY: dc-yarn-build
dc-yarn-build: ## update yarn for build
		docker-compose run --rm encore yarn build

.PHONY: dc-yarn-rebuild-sass
dc-yarn-rebuild-sass: ## rebuild node sass (sometimes can be error)
		docker-compose run --rm encore npm rebuild node-sass --force
