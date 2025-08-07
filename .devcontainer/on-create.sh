#!/bin/bash

set -ev
set -o xtrace

PACKAGE=`cat composer.json | jq -r '.name'`
PROFILE_NAME=`ls | grep info.yml | sed 's/\.info\.yml//'`
PROFILE_BRANCH=`git rev-parse --abbrev-ref HEAD`

rm -rf ./*

chown -R www-data:www-data /tmp
rm -rf /var/www/html
git clone --branch 2.x https://github.com/SU-SWS/acsf-cardinalsites-public.git /var/www/html
cp .devcontainer/drush.yml /var/www/html/drush/local.drush.yml

cd /var/www/html
composer require "$PACKAGE:dev-$PROFILE_BRANCH || $PROFILE_BRANCH-dev" --no-update &&
composer update --no-interaction
rm -rf docroot/*/custom/*
composer install --prefer-source --no-interaction

