#!/usr/bin/env bash
# Runs once, when the container is created: builds and installs the site.
set -euo pipefail
"$(dirname "$0")/build-site.sh"
