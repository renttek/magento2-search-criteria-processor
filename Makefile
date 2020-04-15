ifndef VERBOSE
.SILENT:
endif

all: static test
test: test-unit
ci: static test-unit-ci

# .PHONY: test-unit
test-unit:
	vendor/bin/phpunit --testsuite Unit --order-by=random --prepend test/xdebug-filter.php

test-unit-ci:
	vendor/bin/phpunit --testsuite Unit --order-by=random --prepend test/xdebug-filter.php --color=never --coverage-text

MYSQL_PORT=32123
MYSQL_PING=mysqladmin ping -h127.0.0.1 -P$(MYSQL_PORT) -uroot -ptesting --silent 2>/dev/null
test-integration:
	docker-compose up -d db
	echo -n "waiting for mysql container"
	while ! $(MYSQL_PING); do sleep 1 && echo -n '.'; done
	DB_PORT=$(MYSQL_PORT) vendor/bin/phpunit --testsuite Integration --order-by=random --prepend test/xdebug-filter.php
	docker-compose stop -t 0 db
	docker-compose rm -f db

static:
	echo "Linting files"
	find src -name '*.php' -print0 | xargs -0 -n1 php -l 1>/dev/null
	find src -name '*.phtml' -print0 | xargs -0 -n1 php -l 1>/dev/null
	find test -name '*.php' -print0 | xargs -0 -n1 php -l 1>/dev/null

	echo "Running CodeSniffer"
	vendor/bin/phpcs --standard=psr2 src
	vendor/bin/phpcs --standard=psr2 test

	echo "Running MessDetector"
	vendor/bin/phpmd src text cleancode,codesize,design,unusedcode
	vendor/bin/phpmd test text cleancode,codesize,design,unusedcode

	echo "Running PHPStan"
	vendor/bin/phpstan analyse --no-progress --configuration=phpstan.neon
