#!/usr/bin/env bash
# Build a Drupal site around this repository and install the profile, with
# stanford-caravan's CI scripts (the same build the GitHub Actions tests use).
# The site is at /var/www/site/drupal, with web/profiles/custom/<profile>
# linked to this repository, so changes here show up on the site.
#
# Run it again to rebuild from scratch; it replaces the site and its database.
#
# Environment:
#   CARAVAN_REF   stanford-caravan branch or tag. Default 12.x.
#   CORE_VERSION  Drupal core version, as in caravan's build.sh. Default ^11.
set -euo pipefail

REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
SITE_DIR=/var/www/site/drupal
CARAVAN_DIR=/var/www/site/stanford-caravan
PROFILE="$(basename "$(find "$REPO_DIR" -maxdepth 1 -name '*.info.yml' | head -n 1)" .info.yml)"

rm -rf "$CARAVAN_DIR"
git clone --quiet --depth 1 --branch "${CARAVAN_REF:-12.x}" \
  https://github.com/SU-SWS/stanford-caravan.git "$CARAVAN_DIR"

WITH_CODECEPTION=1 "$CARAVAN_DIR/ci/build.sh" "$REPO_DIR" "$SITE_DIR"

# Use this repository instead of the build's copy of it.
rm -rf "$SITE_DIR/web/profiles/custom/$PROFILE"
ln -s "$REPO_DIR" "$SITE_DIR/web/profiles/custom/$PROFILE"

"$CARAVAN_DIR/ci/install.sh" "$REPO_DIR" "$SITE_DIR"

# Drush's URI: the Codespaces forwarded port, or localhost.
if [ -n "${CODESPACE_NAME:-}" ]; then
  uri="https://${CODESPACE_NAME}-80.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
else
  uri=http://localhost
fi
mkdir -p "$SITE_DIR/drush"
printf 'options:\n  uri: %s\n' "$uri" > "$SITE_DIR/drush/drush.yml"

cd "$SITE_DIR"
vendor/bin/drush user:create sws --password=sws -y > /dev/null 2>&1 || true
vendor/bin/drush user:role:add administrator sws -y
echo "The site is at $uri. Log in as sws / sws, or run: drush uli"
