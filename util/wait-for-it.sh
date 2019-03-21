until pg_isready -h ${DB_HOST} -p ${DB_PORT} -d ${DB_DATABASE}; do
    echo "$(date) - waiting for postgres database ${DB_DATABASE} on ${DB_HOST}:${DB_PORT}..."
    sleep 1
done

echo "Postgres is ready - executing $@"

exec "$@"