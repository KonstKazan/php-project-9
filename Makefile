PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public public/index.php

install:
	composer install

console:
	composer exec --verbose psysh

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public
	composer exec --verbose phpstan analyse public

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 public

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text
