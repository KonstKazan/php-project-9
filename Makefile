PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

install:
	composer install

validate:
	composer validate --no-check-publish

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public src
	composer exec --verbose phpstan analyse public src

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 public src