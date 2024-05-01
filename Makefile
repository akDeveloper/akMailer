build:
	docker build -f .docker/php8.3-cli/Dockerfile -t akmailer-8.3 .
composer-update:
	docker run -v $(shell pwd):/opt/php akmailer-8.3 sh -c 'composer update'
