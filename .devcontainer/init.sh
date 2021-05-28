#!/bin/bash

set -e

composer install -n
rm -rf docroot/*/custom/*
git checkout docroot/
composer install --prefer-source -n

cp .gitpod/blt.yml blt/local.blt.yml
vendor/bin/blt blt:telemetry:disable -n
vendor/bin/blt settings
vendor/bin/blt source:build:simplesamlphp-config
vendor/bin/blt drupal:install -n

# Implement later: import user's SSH key using vault instead.
#export VAULT_ADDR='https://vault.stanford.edu'
#vault login -method=ldap username=$VAULT_USER password=$VAULT_PASS
