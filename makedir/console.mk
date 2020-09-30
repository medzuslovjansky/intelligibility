.PHONY: dc-php-console
dc-php-console: ## open php container
		docker-compose run --rm php sh -lc 'bash'

.PHONY: dc-nginx-console
dc-nginx-console: ## open nginx container
		docker-compose run --rm nginx sh -lc 'bash'

.PHONY: dc-nginx-logs
dc-nginx-logs: ## show nginx logs
		docker-compose logs nginx