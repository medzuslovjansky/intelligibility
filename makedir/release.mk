.PHONY: dc-release
dc-release: enable-config-prod dc-rebuild dc-comp-inst-o dc-cc dc-db-update dc-yarn-install dc-yarn-build

.PHONY: dc-release-dev
dc-release-dev: enable-config-dev dc-rebuild dc-comp-inst dc-cc dc-db-update dc-yarn-install dc-yarn-dev

.PHONY: enable-config-dev
enable-config-dev: ## enable all dev configs
		sh oam/runDev.sh

.PHONY: enable-config-prod
enable-config-prod: ## enable all prod configs
		./oam/runProd.sh
