#!/bin/bash

set -ev
set -o xtrace

PROFILE_NAME=`ls | grep info.yml | sed 's/\.info.*//'`

rm -rf /workspaces/html/docroot/profiles/custom/$PROFILE_NAME
ln -snf /workspaces/$PROFILE_NAME /workspaces/html/docroot/profiles/custom/$PROFILE_NAME

if [[ ! -z $SSH_PRIVATE_KEY ]]; then
  mkdir -p ~/.ssh
  echo $SSH_PRIVATE_KEY | base64 -d > ~/.ssh/id_rsa
  chmod 600 ~/.ssh/id_rsa
fi
if [[ ! -z $GITCONFIG ]]; then
  echo $GITCONFIG | base64 -d > ~/.gitconfig
  chmod 644 ~/.gitconfig
fi

cd /workspaces/html && drush uli

