#!/bin/sh
set -e

cd /app

mkdir -p \
    storage/app/private \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chmod -R ug+rw storage bootstrap/cache

if [ -z "${APP_KEY}" ]; then
    echo "WARNING: APP_KEY is empty. Generating an ephemeral key for this container."
    echo "         Set APP_KEY in your .env so sessions and encrypted data survive restarts."
    export APP_KEY="$(php artisan key:generate --show)"
fi

# Wait for MariaDB to accept connections before touching the database.
case "${DB_CONNECTION}" in
    mysql | mariadb)
        echo "Waiting for database at ${DB_HOST}:${DB_PORT}..."
        attempt=0
        until php -r 'exit(@fsockopen(getenv("DB_HOST"), (int) getenv("DB_PORT")) ? 0 : 1);'; do
            attempt=$((attempt + 1))
            if [ "${attempt}" -ge 60 ]; then
                echo "Database did not become reachable in time." >&2
                exit 1
            fi
            sleep 2
        done
        echo "Database is up."
        ;;
esac

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

php artisan storage:link || true

if [ "${APP_ENV}" = "production" ]; then
    php artisan optimize
else
    php artisan optimize:clear
fi

exec "$@"
