# [Cardinal Service Profile](hhttps://github.com/SU-HKKU/cardinal_service_profile)
##### 8.x
[![CircleCI](https://circleci.com/gh/SU-HKKU/cardinal_service_profile.svg?style=shield)](https://circleci.com/gh/SU-HKKU/cardinal_service_profile)

[![Maintainability](https://api.codeclimate.com/v1/badges/4bcf8ab986d6837c7d97/maintainability)](https://codeclimate.com/github/SU-HKKU/cardinal_service_profile/maintainability)

[![Test Coverage](https://api.codeclimate.com/v1/badges/4bcf8ab986d6837c7d97/test_coverage)](https://codeclimate.com/github/SU-HKKU/cardinal_service_profile/test_coverage)


Maintainers: [Mike Decker](https://github.com/pookmish), [sherakama](https://github.com/sherakama)

Changelog: [Changelog.md](CHANGELOG.md)

Description
---

This is the installation profile for Cardinal Service. It is based on and requires the [Stanford Profile](https://github.com/SU-SWS/stanford_profile).
A nightly task will pull updates from  stanford profile and produce a pull request in github for testing and review.

Profile specific documentation is available in the [help](help) directory. The help documentation in that directory
is available in the UI at `/admin/help/cardinal-service`. When creating new help documenation, ensure there is an `h1`
within the file. An `h1` is denoted by a single `#` at the beginning of the file. Each help file produces its own path
available to the site managers.

Accessibility
---
[![WCAG Conformance 2.0 AA Badge](https://www.w3.org/WAI/wcag2AA-blue.png)](https://www.w3.org/TR/WCAG20/)
This module conforms to level AA WCAG 2.0 standards as required by the university's accessibility policy. For more information on the policy please visit: [https://ucomm.stanford.edu/policies/accessibility-policy.html](https://ucomm.stanford.edu/policies/accessibility-policy.html).

Installation
---

Install this installation profile like any other profile. [See Drupal Documentation](https://www.drupal.org/docs/7/install/using-an-installation-profile)

Configuration
---

Nothing special needed.

Inheriting From Stanford Profile
---
1. `git pull https://github.com/SU-SWS/stanford_profile.git 11.x -X ours --no-edit`
2. Fix any conflicts if they occur and commit those changes
3. Some adjustments are possible, resolve those until the automated tests pass.

Releases
---

Steps to build a new release:
- Checkout the latest commit from the `8.x-1.x` branch.
- Create a new branch for the release.
- Commit any necessary changes to the release branch.
  -  These may include, but are not necessarily limited to:
    - Update the version in any `info.yml` files, including in any submodules.
    - Update the CHANGELOG to reflect the changes made in the new release.
- Make a PR to merge your release branch into `main`
- Give the PR a semver-compliant label, e.g., (`patch`, `minor`, `major`).  This may happen automatically via Github actions (if a labeler action is configured).
- When the PR is merged to `main`, a new tag will be created automatically, bumping the version by the semver label.
- The github action is built from: [semver-release-action](https://github.com/K-Phoen/semver-release-action), and further documentation is available there.


Troubleshooting
---

If you are experiencing issues with this try posting an issue on the [GitHub issues page](https://github.com/SU-SWS/stanford_profile/issues).

Contribution / Collaboration
---

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request. For more help please see [GitHub's article on fork, branch, and pull requests](https://help.github.com/articles/using-pull-requests)
