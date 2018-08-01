#!/usr/bin/env bash

docker exec -it fssk-laravel-server cd server && ./vendor/bin/phpunit
