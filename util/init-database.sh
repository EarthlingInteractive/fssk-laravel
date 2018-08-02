#!/usr/bin/env bash

docker exec -it fssk-laravel-server php server/artisan migrate --seed
