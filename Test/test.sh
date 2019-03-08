#!/bin/bash

[[ $TRAVIS_COMMIT_MESSAGE =~ ^(\[tests skip\]) ]] && echo "TESTS SKIP" && exit 0;

cd $TRAVIS_BUILD_DIR
cp .env.sample .env
chmod +x $(pwd)/.scripts/install.sh && $(pwd)/.scripts/install.sh

cd $TRAVIS_BUILD_DIR/Test/e2e

npm ci
npx cypress run --config video=false --project ./magento2 -s ./magento2/cypress/integration/sample_spec.js
