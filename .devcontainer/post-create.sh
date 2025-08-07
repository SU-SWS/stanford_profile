#!/bin/bash

set -ev
set -o xtrace


#rm -rf docroot/profiles/custom/$PROFILE_NAME
#ln -snf /workspaces/$PROFILE_NAME docroot/profiles/custom/$PROFILE_NAME
#
#drush sws:multisite:settings
#sed -i "s|uri:.*$|uri: https://$CODESPACE_NAME-80.app.github.dev|" docroot/sites/default/local.drush.yml
#sed -i "s|uri:.*$|uri: https://$CODESPACE_NAME-80.app.github.dev|" drush/local.drush.yml
#
#drush site-install $PROFILE_NAME -y -v
#drush cim -y
#
#if [[ ! -z $SSH_PRIVATE_KEY ]]; then
#  mkdir -p ~/.ssh
#  echo $SSH_PRIVATE_KEY | base64 -d > ~/.ssh/id_rsa
#  chmod 600 ~/.ssh/id_rsa
#fi
#if [[ ! -z $GITCONFIG ]]; then
#  echo $GITCONFIG | base64 -d > ~/.gitconfig
#  chmod 644 ~/.gitconfig
#fi
#
#drush cset stage_file_proxy.settings origin 'localhost' -y
#drush uli

