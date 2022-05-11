# CircleCI Setup

1. Add the project to [CodeClimate](https://codeclimate.com/oss/dashboard)
1. In CodeClimate, under the "Repo Settings" tab, do the following
   1. on the "Test coverage" page, disable “Enforce total coverage” and set the "Enforce Diff Coverage" to 90
   1. on the "Github" page, disable "Summary comments" and "Inline issue comments"
   1. On The "Test Coverage" page, copy or record the "TEST REPORTER ID" for later.
1. Go to https://circleci.com/add-projects/gh/SU-SWS find the repository to add and click “set up project”
1. Click “Start Building”
1. Go to the environment settings at https://circleci.com/gh/SU-SWS/[repo-name]/edit#env-vars
1. Click “add variable”
1. For the variable name enter CC_TEST_REPORTER_ID
1. For the variable value enter the value found in codeclimate from earlier.
1. Save the variable
1. Go to the “Advanced Settings” in circleci https://circleci.com/gh/SU-SWS/[repo-name]/edit#advanced-settings
1. Turn on “GitHub Status updates”
1. Turn on “Auto-cancel redundant workflow”
1. Go to the “Status Badges” in circleci https://circleci.com/gh/SU-SWS/\[repo-name]/edit#badges
1. copy the markdown text and place it into the README.md file of the repo
1. Copy the .circleci directory from stanford-caravan/config directory and place it at the root of the repository. The repository should contain a .circleci/config.yml file
1. Commit to the repository and view code climate
