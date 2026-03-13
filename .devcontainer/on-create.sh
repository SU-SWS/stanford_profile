#!/bin/bash

set -ev
set -o xtrace

PACKAGE=`cat composer.json | jq -r '.name'`
PROFILE_NAME=`ls | grep info.yml | sed 's/\.info\.yml//'`
PROFILE_BRANCH=`git rev-parse --abbrev-ref HEAD`

chown -R www-data:www-data /tmp
rm -rf /var/www/html
git clone --branch 2.x https://github.com/SU-SWS/acsf-cardinalsites-public.git /workspaces/html
ln -snf /workspaces/html /var/www/html

cp .devcontainer/drush.yml /var/www/html/drush/local.drush.yml

cd /var/www/html
composer require "$PACKAGE:dev-$PROFILE_BRANCH || $PROFILE_BRANCH-dev" --no-update &&
composer update --no-interaction
rm -rf docroot/*/custom/*
composer install --prefer-source --no-interaction

drush sws:multisite:settings
sed -i "s|uri:.*$|uri: https://$CODESPACE_NAME-80.app.github.dev|" docroot/sites/default/local.drush.yml
sed -i "s|uri:.*$|uri: https://$CODESPACE_NAME-80.app.github.dev|" drush/local.drush.yml

cp .gitpod/global.settings.php docroot/sites/settings/global.settings.php
cp .gitpod/default.local.services.yml docroot/sites/local.services.yml

drush site-install $PROFILE_NAME -y -v
drush cset stage_file_proxy.settings origin 'localhost' -y
