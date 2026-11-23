#!/usr/bin/env bash
# Runs every time the container starts.
set -euo pipefail

sudo apache2ctl start || true

# Optional Codespaces secrets: an SSH key for remote services, and a git config.
if [ -n "${SSH_PRIVATE_KEY:-}" ]; then
  mkdir -p ~/.ssh
  echo "$SSH_PRIVATE_KEY" | base64 -d > ~/.ssh/id_rsa
  chmod 600 ~/.ssh/id_rsa
fi
if [ -n "${GITCONFIG:-}" ]; then
  echo "$GITCONFIG" | base64 -d > ~/.gitconfig
  chmod 644 ~/.gitconfig
fi
