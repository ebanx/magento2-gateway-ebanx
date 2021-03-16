#!/bin/sh

set -e

/usr/local/bin/wait-for-it.sh -t 60 $MYSQL_HOST:$MYSQL_PORT -- echo 'MySQL is up!'
/usr/local/bin/wait-for-it.sh -t 60 $ELASTICSEARCH_HOST:$ELASTICSEARCH_PORT -- echo 'ElasticSearch is up!'

# install magento
php -d memory_limit=-1 bin/magento setup:install \
    --base-url=$MAGENTO_URL \
    --db-host=$MYSQL_HOST \
    --db-name=$MYSQL_DATABASE \
    --db-user=$MYSQL_USER \
    --db-password=$MYSQL_ROOT_PASSWORD \
    --search-engine=elasticsearch7 \
    --elasticsearch-host=$ELASTICSEARCH_HOST \
    --elasticsearch-port=$ELASTICSEARCH_PORT \
    --elasticsearch-enable-auth=0 \
    --admin-firstname=$MAGENTO_ADMIN_FIRSTNAME \
    --admin-lastname=$MAGENTO_ADMIN_LASTNAME \
    --admin-email=$MAGENTO_ADMIN_EMAIL \
    --admin-user=$MAGENTO_ADMIN_USERNAME \
    --admin-password=$MAGENTO_ADMIN_PASSWORD \
    --language=$MAGENTO_LANGUAGE \
    --currency=$MAGENTO_DEFAULT_CURRENCY \
    --timezone=$MAGENTO_TIMEZONE \
    --use-rewrites=1 \
    --backend-frontname=$MAGENTO_BACKEND_FRONTNAME \
    --use-secure=$MAGENTO_USE_SECURE \
    --use-sample-data

# deploy sample data and static content
#php -d memory_limit=-1 bin/magento setup:static-content:deploy -f

php -d memory_limit=-1 bin/magento module:disable Magento_TwoFactorAuth
php -d memory_limit=-1 bin/magento sampledata:deploy
php -d memory_limit=-1 bin/magento setup:di:compile
php -d memory_limit=-1 bin/magento setup:upgrade
php -d memory_limit=-1 bin/magento cache:flush
php -d memory_limit=-1 bin/magento setup:static-content:deploy -f

echo "Everything is fine... Magento is live in ${MAGENTO_URL}"

apache2-foreground