#!/bin/bash

[[ $TRAVIS_COMMIT_MESSAGE =~ ^(\[tests skip\]) ]] && echo "TESTS SKIP" && exit 0;

cd $TRAVIS_BUILD_DIR
cp .env.sample .env
chmod +x $(pwd)/.scripts/install.sh && $(pwd)/.scripts/install.sh
