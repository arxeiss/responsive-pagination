#!make

help:
	@echo "Project aliases and shortcuts."

	@echo "\nTests"
	@echo "  make test [TESTS=TESTS]           - Run all tests or specify them in 'params' optional parameter"
	@echo "  make test:unit                     - Run phpunit tests"

	@echo "\nCode style and quality"
	@echo "  make phpcbf                        - Run CodeSniffer"
	@echo "  make lint                          - Run CodeSniffer Beautifier"
	@echo "  make phpstan                       - Run PHP STAN"

all:
	make lint
	make phpstan
	make test

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

phpstan:
	./vendor/bin/phpstan analyse $(PARAMS)
