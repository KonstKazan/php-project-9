PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public public/index.php

install:
	composer install

console:
	composer exec --verbose psysh

validate:
	composer validate --no-check-publish

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public
	composer exec --verbose phpstan analyse public

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 public