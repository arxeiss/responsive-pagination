#!make

all:
	@echo "Project aliases and shortcuts."

	@echo "\nTests"
	@echo "  make test [TESTS=TESTS]           - Run all tests or specify them in 'params' optional parameter"
	@echo "  make test:unit                     - Run phpunit tests"

	@echo "\nCode style and quality"
	@echo "  make phpcbf                        - Run phpcbf/codesniffer"
	@echo "  make lint                          - Run phpcs/codesniffer"

help:
	make all

test:
	./vendor/bin/phpunit $(TESTS)

test\:unit:
	./vendor/bin/phpunit tests/Units/

lint:
	./vendor/bin/phpcs --standard=./phpcs.xml $(PARAMS)

phpcbf:
	./vendor/bin/phpcbf --standard=./phpcs.xml $(PARAMS)

phpcs:
	make lint
