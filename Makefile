.PHONY: lint cs cs-fix phpstan tests qa

qa: lint cs phpstan tests

lint:
	vendor/bin/parallel-lint -e php,phpt --exclude tests/temp src tests

cs:
	vendor/bin/phpcs

cs-fix:
	vendor/bin/phpcbf

phpstan:
	vendor/bin/phpstan analyse
	vendor/bin/phpstan analyse -c phpstan.tests.neon.dist

tests:
	vendor/bin/tester -c tests/php.ini -s tests
