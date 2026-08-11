#!/bin/sh
# Runs once, on first initialization of an empty mariadb data directory.
#
# The app user is created by the image with privileges on MARIADB_DATABASE only,
# so it cannot CREATE/DROP the per-tenant databases that stancl/tenancy needs.
# This grants it full rights over any database matching the tenant prefix, which
# is narrower than granting *.*.
#
# The `tenant%` pattern must match tenancy.database.prefix in config/tenancy.php.
# If you change that prefix, change this pattern too.
set -e

mariadb -uroot -p"${MARIADB_ROOT_PASSWORD}" <<SQL
GRANT ALL PRIVILEGES ON \`tenant%\`.* TO '${MARIADB_USER}'@'%';
FLUSH PRIVILEGES;
SQL

echo "Granted '${MARIADB_USER}' full privileges on tenant% databases."
