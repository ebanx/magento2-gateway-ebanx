#!/bin/bash

source $(pwd)/.env

echo "Shutting down containers..."
docker-compose down

echo "Removing .db-data folder..."
rm -rf $(pwd)/.db-data

echo "Starting containers..."
docker-compose up -d
sleep 20

echo "Installing magento..."
docker exec -it ${PWD##*/}_web_1 install-magento

echo "Importing sample data... This will probably take a long time..."
docker exec -it ${PWD##*/}_web_1 install-sampledata

echo "Running composer install... This also seems to take a long time..."
docker exec -it ${PWD##*/}_web_1 composer require ebanx/magento2-gateway-ebanx
docker exec -it ${PWD##*/}_web_1 composer update

echo "Everything is fine... Magento is live in ${MAGENTO_URL}"
