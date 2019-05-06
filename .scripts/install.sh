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
docker exec -it magento2-gateway-ebanx_web_1 install-magento

echo "Importing sample data... This will probably take a long time..."
docker exec -it magento2-gateway-ebanx_web_1 install-sampledata

echo "Running composer install... This also seems to take a long time..."
docker exec -it magento2-gateway-ebanx_web_1 composer require ebanx/benjamin
docker exec -it magento2-gateway-ebanx_web_1 composer update
docker exec -it magento2-gateway-ebanx_web_1 php bin/magento deploy:mode:set production
docker exec -it magento2-gateway-ebanx_web_1 php bin/magento c:c
docker exec -it magento2-gateway-ebanx_web_1 php bin/magento c:f

echo "Everything is fine... Magento is live in ${MAGENTO_URL}"
