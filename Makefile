export PHP := 8.3
export COMPOSER_FLAGS := --prefer-stable --quiet

test-all:
	@make test PHP=8.2 COMPOSER_FLAGS="--quiet --prefer-lowest"
	@make test PHP=8.2 COMPOSER_FLAGS="--quiet --prefer-stable"
	@make test PHP=8.3 COMPOSER_FLAGS="--quiet --prefer-lowest"
	@make test PHP=8.3 COMPOSER_FLAGS="--quiet --prefer-stable"

test:
	@make up
	@make install-vendor
	@docker compose exec php-${PHP} vendor/bin/phpunit
	@make down

install-vendor:
	@docker compose exec php-${PHP} composer update ${COMPOSER_FLAGS}

up:
	@docker compose up -d php-${PHP}

down:
	@docker compose down --remove-orphans
