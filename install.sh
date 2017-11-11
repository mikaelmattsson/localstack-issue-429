#!/usr/bin/env bash

docker-compose up -d --force-recreate localstack

docker-compose run --rm app composer install

docker-compose run --rm app php setup.php
