#!/bin/bash

set -ev
set -o xtrace

PROFILE_BRANCH=`git rev-parse --abbrev-ref HEAD`

chown -R www-data:www-data /tmp
rm -rf /var/www/html
git clone --branch 2.x https://github.com/SU-SWS/acsf-cardinalsites-public.git /var/www/html
cp .devcontainer/drush.yml /var/www/html/drush/local.drush.yml

cd /var/www/html
composer require su-sws/stanford_profile:"dev-$PROFILE_BRANCH || $PROFILE_BRANCH-dev" --no-update &&
composer update --no-interaction
rm -rf docroot/*/custom/*
composer install --prefer-source --no-interaction

rm -rf docroot/profiles/custom/stanford_profile
ln -snf /workspace/stanford_profile docroot/profiles/custom/stanford_profile

drush sws:multisite:settings
sed -i "s|uri:.*$|uri: https://$CODESPACE_NAME-80.app.github.dev|" docroot/sites/default/local.drush.yml
sed -i "s|uri:.*$|uri: https://$CODESPACE_NAME-80.app.github.dev|" drush/local.drush.yml

drush site-install stanford_profile -y -v
drush cim -y

if [[ ! -z $SSH_PRIVATE_KEY ]]; then
  mkdir -p ~/.ssh
  echo $SSH_PRIVATE_KEY | base64 -d > ~/.ssh/id_rsa
  chmod 600 ~/.ssh/id_rsa
  ssh-keyscan staging-2153.enterprise-g1.hosting.acquia.com >> ~/.ssh/known_hosts
  drush sws:keys
fi
if [[ ! -z $GITCONFIG ]]; then
  echo $GITCONFIG | base64 -d > ~/.gitconfig
  chmod 644 ~/.gitconfig
fi

drush cset stage_file_proxy.settings origin 'localhost' -y
drush uli
