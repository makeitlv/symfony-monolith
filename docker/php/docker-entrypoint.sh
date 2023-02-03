#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	# Install the project the first time PHP is started
	# After the installation, the following block can be deleted
	if [ ! -f composer.json ]; then
		CREATION=1

		rm -Rf tmp/
		composer create-project "symfony/skeleton $SYMFONY_VERSION" tmp --stability="$STABILITY" --prefer-dist --no-progress --no-interaction --no-install

		cd tmp
		composer require "php:>=$PHP_VERSION"
		composer config --json extra.symfony.docker 'true'
		cp -Rp . ..
		cd -

		rm -Rf tmp/
	fi

	if [ ! -f bin/phpcs.phar ]; then
		wget -O bin/phpcs.phar https://github.com/squizlabs/PHP_CodeSniffer/releases/download/3.7.1/phpcs.phar

		chmod +x bin/phpcs.phar
	fi

	if [ ! -f bin/phpcbf.phar ]; then
		wget -O bin/phpcbf.phar https://github.com/squizlabs/PHP_CodeSniffer/releases/download/3.7.1/phpcbf.phar

		chmod +x bin/phpcbf.phar
	fi

	if [ ! -f bin/phpcpd.phar ]; then
		wget -O bin/phpcpd.phar https://phar.phpunit.de/phpcpd-6.0.3.phar

		chmod +x bin/phpcpd.phar
	fi

	if [ ! -f bin/psalm.phar ]; then
		wget -O bin/psalm.phar https://github.com/vimeo/psalm/releases/download/5.6.0/psalm.phar

		chmod +x bin/psalm.phar
	fi

	if [ ! -f bin/deptrac.phar ]; then
		wget -O bin/deptrac.phar https://github.com/qossmic/deptrac/releases/download/1.0.2/deptrac.phar

		chmod +x bin/deptrac.phar
	fi

	if [ ! -f bin/phpmetrics.phar ]; then
		wget -O bin/phpmetrics.phar https://github.com/phpmetrics/PhpMetrics/releases/download/v2.8.1/phpmetrics.phar

		chmod +x bin/phpmetrics.phar
	fi

	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

	if grep -q ^DATABASE_URL= .env; then
		# After the installation, the following block can be deleted
		if [ "$CREATION" = "1" ]; then
			echo "To finish the installation please press Ctrl+C to stop Docker Compose and run: docker compose up --build"
			sleep infinity
		fi

		echo "Waiting for db to be ready..."
		ATTEMPTS_LEFT_TO_REACH_DATABASE=60
		until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(bin/console dbal:run-sql "SELECT 1" 2>&1); do
			if [ $? -eq 255 ]; then
				# If the Doctrine command exits with 255, an unrecoverable error occurred
				ATTEMPTS_LEFT_TO_REACH_DATABASE=0
				break
			fi
			sleep 1
			ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
			echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
		done

		if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
			echo "The database is not up or not reachable:"
			echo "$DATABASE_ERROR"
			exit 1
		else
			echo "The db is now ready and reachable"
		fi

		if [ "$( find ./migrations -iname '*.php' -print -quit )" ]; then
			bin/console doctrine:migrations:migrate --no-interaction
		fi
	fi

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
