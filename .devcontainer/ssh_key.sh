#!/bin/bash

if [ -f /workspace/.ssh/id_rsa ]; then
  eval `ssh-agent -s`
  ssh-add -l | grep -q ida_rsa || ssh-add /workspace/.ssh/id_rsa
  exit
fi

echo 'Please paste your ssh private key or enter to skip (Skipping will prevent some functionality)'

IFS= read -d '' -n 1 SSH_KEY
while IFS= read -d '' -n 1 -t 2 c
do
    SSH_KEY+=$c
done

mkdir -p /workspace/.ssh
rm -f /workspace/.ssh/id_rsa
if [[ "$SSH_KEY" =~ [A-Za-z] ]]; then
  printf -- "$SSH_KEY" >> /workspace/.ssh/id_rsa
  # Ensure a empty space at the end of the file
  echo '' >> /workspace/.ssh/id_rsa
  chmod 600 /workspace/.ssh/id_rsa
  eval `ssh-agent -s`
  ssh-add /workspace/.ssh/id_rsa
fi
