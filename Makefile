# Project specific variables
CONSOLE			:= bin/console

##
## ---------------------------------------------------------------------------
## Deployment
## ---------------------------------------------------------------------------
##

deploy-prod: ## Deploy in production
	composer dump-env prod
	composer install --prefer-dist --optimize-autoloader --no-dev
	php bin/console assets:install --env prod
	yarn install --force
	yarn encore production
	aws s3 sync public/ s3://portfolio-dev-eu-west-3-6rwlxnj8r9/ --delete --exclude index.php
	LAMBDA_TASK_ROOT=bref php bin/console cache:warmup --env=prod --no-debug
	serverless deploy
	rm .env.local.php

.PHONY: deploy-prod

##
## ---------------------------------------------------------------------------
## Assets
## ---------------------------------------------------------------------------
##

asset-prod: ## Compile assets for production
	php bin/console assets:install --env prod
	yarn encore production

.PHONY: asset-prod

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
		| sed -e 's/^.*Makefile://g' \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' \
		| sed -e 's/\[32m##/[33m/'
.PHONY: help
