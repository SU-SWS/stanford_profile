# [Stanford Text Editor](https://github.com/SU-SWS/stanford_text_editor)
##### Version: 8.x

[![CircleCI](https://circleci.com/gh/SU-SWS/stanford_text_editor.svg?style=svg)](https://circleci.com/gh/SU-SWS/stanford_text_editor)
[![Maintainability](https://api.codeclimate.com/v1/badges/3db3047a28d5bd9058db/maintainability)](https://codeclimate.com/github/SU-SWS/stanford_text_editor/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/3db3047a28d5bd9058db/test_coverage)](https://codeclimate.com/github/SU-SWS/stanford_text_editor/test_coverage)

Maintainers: [jbickar](https://github.com/jbickar), [sherakama](https://github.com/sherakama)
[Changelog.txt](CHANGELOG.txt)

This module contains the configuration options for WYSIWYG editors and input formats.

Installation
---

Install this module like any other module. [See Drupal Documentation](https://www.drupal.org/docs/8/extending-drupal-8/installing-contributed-modules-find-import-enable-configure-drupal-8)

Configuration
---

Nothing special needed.

Troubleshooting
---

If you are experiencing issues with this module try reverting the feature first. If you are still experiencing issues try posting an issue on the GitHub issues page.

Contribution / Collaboration
---

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request. For more help please see [GitHub's article on fork, branch, and pull requests](https://help.github.com/articles/using-pull-requests)


Releases
---

Steps to build a new release:
- Checkout the latest commit from the `8.x-1.x` branch.
- Create a new branch for the release.
- Commit any necessary changes to the release branch.
  -  These may include, but are not necessarily limited to:
    - Update the version in any `info.yml` files, including in any submodules.
    - Update the CHANGELOG to reflect the changes made in the new release.
- Make a PR to merge your release branch into `master`
- Give the PR a semver-compliant label, e.g., (`patch`, `minor`, `major`).  This may happen automatically via Github actions (if a labeler action is configured).
- When the PR is merged to `master`, a new tag will be created automatically, bumping the version by the semver label.
- The github action is built from: [semver-release-action](https://github.com/K-Phoen/semver-release-action), and further documentation is available there.
