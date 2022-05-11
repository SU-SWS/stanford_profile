# [Stanford News](https://github.com/SU-SOE/stanford_news)
##### Version: 8.x-2.x

[![CircleCI](https://circleci.com/gh/SU-SWS/stanford_news.svg?style=shield)](https://circleci.com/gh/SU-SWS/stanford_news)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d22dd5e41d0a5c9198fd/test_coverage)](https://codeclimate.com/github/SU-SWS/stanford_news/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/d22dd5e41d0a5c9198fd/maintainability)](https://codeclimate.com/github/SU-SWS/stanford_news/maintainability)

Maintainers: [boznik](https://github.com/boznik)

Changelog: [Changelog.md](CHANGELOG.md)

Description
---

Stanford News feature provides an out of the box solution for displaying news content on your website. This feature contains a content type, fields, a news page layout, and taxonomy. This module is a great replacement for the default Article content type.

Accessibility
---
[![WCAG Conformance 2.0 AA Badge](https://www.w3.org/WAI/wcag2AA-blue.png)](https://www.w3.org/TR/WCAG20/)
Evaluation Date: N/A
This module conforms to level AA WCAG 2.0 standards as required by the university's accessibility policy. For more information on the policy please visit: [https://ucomm.stanford.edu/policies/accessibility-policy.html](https://ucomm.stanford.edu/policies/accessibility-policy.html).

Installation
---

Install this module like any other module. [See Drupal Documentation](https://drupal.org/documentation/install/modules-themes/modules-8)

Configuration
---

- Pull in this branch.
- Build.
- Add terms to the Topics taxonomy. They have a generated path.
- Create a menu with your taxonomy terms.
- Go to /admin/structure/types/manage/stanford_page/display and allow Topics Menus block to be added via the Layout Builder.
- Create articles and tag them with a term from the taxonomy.
- Create term list pages.
- They need paths like this: /news/term-1 (term name) or /news/all
- On this page, use the layout builder to add your menu.
- On this page, add the News Terms block.
- On this page, add the Newsletter Sign up block.


Troubleshooting
---

If you are experiencing issues with this module try reverting the feature first. If you are still experiencing issues try posting an issue on the GitHub issues page.

Developer
---

If you wish to develop on this module you will most likely need to compile some new css. Please use the sass structure provided and compile with the sass compiler packaged in this module. To install:

```
nvm use
npm install
npm run publish
```

Contribution / Collaboration
---

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request. For more help please see [GitHub's article on fork, branch, and pull requests](https://help.github.com/articles/using-pull-requests)
