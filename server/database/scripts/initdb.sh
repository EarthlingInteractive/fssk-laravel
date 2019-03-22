#!/bin/bash
set -e
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    \c $POSTGRES_DB
    CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
EOSQL
