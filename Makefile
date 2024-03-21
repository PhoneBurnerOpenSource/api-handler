SHELL := bash

app = docker compose run --rm app
composer = $(app) composer

build:
	@docker compose build --pull
	@$(composer) install
	@$(app) mkdir build

.PHONY: clean
clean:
	@rm -rf ./build ./vendor

.PHONY: bash
bash: build
	@$(app) bash

.PHONY: phpunit
phpunit: build
	@$(composer) phpunit

.PHONY: phpcs
phpcs: build
	@$(composer) phpcs

.PHONY: phpcbf
phpcbf: build
	@$(composer) phpcbf

.PHONY: phpstan
phpstan: build
	@$(composer) phpstan

.PHONY: rector
rector: build
	@$(composer) rector

.PHONY: ci
ci: build
	@$(composer) ci
