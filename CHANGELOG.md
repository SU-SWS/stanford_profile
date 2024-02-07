# Cardinal Service Profile

11.0.6
-------------------------------------------------------------------------------

- Update GH Actions checkout (#759)
- Exclude page title on login page
- Add permission to view algolia config settings
- D8CORE-7215 Test to Display the topic menu label on list pages (#758)
- Update address fields for local footer test (#757)
- Update imagemagick config from db update
- Added "fields" to page cache query ignore for jsonapi
- D8CORE-7111 Add local footer social icons for mastodon and google scholar (#756)
- Increase jsonapi page limit to 500 for nodes
- Adjust graphql settings for easier type checking
- Updated DS config for module update
- D8CORE-7205 Trim all html before sending index data to Algolia
- Replaced transliterate_filenames module functionality
- Add and configure graphql compose module (#751)
- Drupal 11.2.0 upgrade (#752)
- D8CORE-7055: added an aria-hidden is true (#745)
- D8CORE-7096 Update algolia search results styles and structure (#747)
- Update github actions (#748)
- Strip more html from algolia indexed items
- Fixed absolute urls for algolia results
- D8CORE-7074 Implement Search API Algolia (#743)
- D8CORE-6726: Overrode the colorbox formatter template to remove aria-label (#746)
- Update alert styles (#744)
- Added cron job config
- D8CORE-2761: reduced padding on filterby menu (#728)
- Updated config split settings

11.0.5
-------------------------------------------------------------------------------
- D8CORE-7066 Adjust site contact fields & language (#741)
- Update contact field labels
- Adjusted access denied message
- Added the anchor for contact details on site settings

11.0.4
-------------------------------------------------------------------------------
- Invalidate cache tag when saving site contact details
- Allow figure and figcaption in embeddables

11.0.2
--------------------------------------------------------------------------------
- D8CORE-7051 Use display field copy instead of token fields

11.0.1
--------------------------------------------------------------------------------
_Release Date: 2023-10-23_

- Next site settings and config ignores (#735)
- D8CORE-7040 Fix source ending tags

11.0.0
--------------------------------------------------------------------------------
_Release Date: 2023-10-20_

- Fix site setting redirect logic
- Use h2 heading for global message
- D8CORE-6976: updated to newest decanter (#730)
- Update config ignore settings for latest module (#729)
- Update local footer config userguide links (#727)
- fixup unit test
- Updated webpack compiler settings
- Added and configured autoprefixer for css compiler
- Updated search api config
- Fixed related policies to avoid self referencing
- SDSS-1007: Add support for global footer variant. (#724)
- Added su-masthead-inner class to masthead <section>. (#725)
- D8CORE-6895: updates for new Decanter and updating webpack (#710)
- Fixed breadcrumbs
- D8CORE-6951  D10 Clean up admin toolbar (#723)
- fixed event subscriber (#303)
- D8CORE-6952: changed to list style none (#719)
- D8CORE-6953: Additional edits to the editing buttons (#722)
- Display field copy released a D10 version
- Lifecycle management contact fields
- D8CORE-6953: fixes to local task tabs (#720)
- D8CORE-6977 Switch to use CSHS instead of simple heiracry select (#721)
- D8CORE-6984 include policy content in sitemap
- Updated editiorially configs
- D8CORE-3718 Minor a11y issues for decoupled main menu
- Updated user mail attribute value for samlauth
- Fixed unit and acceptance tests & D10 Code (#302)
- Drupal 10 Config & test updates (#705)
- D10 Install


10.1.0
--------------------------------------------------------------------------------
_Release Date: 2023-09-20_

- Update samlauth settings when a role is created or deleted (#713)
- Use components field instead of layout builder for list pages (#709)
- D8CORE-4551: updated permissions for contributors and site editors (#690)
- D8CORE-6843: Update bad user guide links in help text (#694)
- Fix configs from inheritance (#300)
- Merge branch '10.x' of https://github.com/SU-SWS/stanford_profile into 10.x
- Fixed styles for login page when on intranets
- D8CORE-6896: changed the non discrimination link (#707)
- D8CORE-6842 Added and configured stanford_samlauth (#701)
- D8CORE-6844 | Fixup mobile menu at medium breakpoint (#703)
- Update localist importer to use the localist json data parser
- fixed test for lazy loading oembed
- D8CORE-6786: Updating to the new font through a Decanter update (#695)
- Add scroll to step to prevent admin toolbar conflict (#698)
- Added and configured Oembed lazyload for video media
- Uninstall page cache query ignore (#297)


10.0.2
--------------------------------------------------------------------------------
_Release Date: 2023-08-10_

- Refactored patterns to be in the modules (#298)
- D8CORE-4495 | Update past events text on node view
- CS-98: Changed contact email on opportunity page to a mailto link. (#296)
- Updated publication importer for layout paragraphs instead of react paragraphs
- Update decoupled menu to recognize "Expanded" menu setting

10.0.1
--------------------------------------------------------------------------------
_Release Date: 2023-07-24_

- Cleaned up automation
- Tweak to the update hook for inheritance
- D8CORE-6816 Restored permissions to run importers for site managers
- Adjust test to run a little faster (#688)
- Add missing chosen library
- Updated google tag settings

10.0.0
--------------------------------------------------------------------------------
_Release Date: 2023-07-18_

- Updating to new decanter version (#682)
- CAP-67 Import profile phone number to the non-mobile phone field
- D8CORE-4539 Removed "Role" attribute on figure element
- D8CORE-6760 | Update ckeditor5 styles (#679)
- D8CORE-6629 use list elements on search result page
- D8CORE-6628 Hide `li` element for mobile search field when on desktop
- D8CORE-6368 Use list elements for related policy display
- D8CORE-5950 Fixed aspect ratio of youtube videos on event pages
- D8CORE-6633 Add skip link anchor to the search page (#681)
- D8CORE-6339 Added main content anchor link destination
- Update banner aspect ratio for small breakpoint (#680)
- D8CORE-6695, 6694 Updated spacer paragraph with optional sizes (#677)
- Added "Admin notes" to the site settings config page
- D8CORE-6457 D8CORE-6476 D8CORE-6477 Tweaks to policy content fields
- Move help section to be below brand bar in header (#674)
- D8CORE-5407 | A11y: Update lockup cell2 max-width, line3 line-height (#672)
- Provide a new replacement menu with a decoupled main menu (#663)
- D8CORE-6416 Update google analytics tracking for stanford_basic theme
- Fix policy typo (#662)
- D8CORE-2622, D8CORE-4494 | Move brand bar and skip-links into header landmark banner (#665)
- D8CORE-6336: changing hover on print icon for policy CT (#661)
- Updated jsonapi endpoints (#664)
- Migrate everything to layout paragraphs and upgrade all modules/themes (#654)
- CS-93: Updated opportunity displays. (#288)
- CS-92: Added sort by label to opportunities list filters (#291)


9.0.0
--------------------------------------------------------------------------------
_Release Date: 2023-03-28_

8.x-3.3
--------------------------------------------------------------------------------
_Release Date: 2022-07-12_

- D8CORE-1835: Added abbr buttons to ckeditor (#550)
- D8CORE-4780 Changed search page button text to "Search"
- D8CORE-5862: Removed obsolete checkbox from theme settings (#575)
- D8CORE-6000: Added additional html elements to embeddables allow list (#584)
- Updated config from search_api module
- D8CORE-5128 Enable embed code validators (#579)
- D8CORE-6005 Allow Span tags in the wysiwyg
- fixed composer namespace to lowercase
- Removed fzaninotto/faker workaround in CI tests
- Fixed Test Configs
- Adjusted functional tests (#577)
- Drupal 9.4 support (#576)
- Move some CircleCi to GH Actions (#568)
- Fixup patches in composer
- D8CORE-5886 Enable ajax on people lists
- Adjusted stanford_profile dependency
- Added and configured ckeditor_blockimagepaste to prevent inline base64 images
- D8CORe-5871 Change order of filter processing to fix <picture><source> tags
- D8CORE-5773: Added edit buttons on courses list page (#555)

8.x-3.2
--------------------------------------------------------------------------------
_Release Date: 2022-05-26_
- CS-87: Adjusted grid styles for more uniform rows. (#258)
- Change the branch to pull from for 8.x branch (#261)
- CS-86: Disabled event opportunities importer (#257)
- Changed Events page menu block. (#254)
- Locked stanford_profile to 8.4.4 constraint for 8.x branch (#255)
- Stanford Profile updates 2022-05-04
- Added courses department importer for long name population
- D8CORE-2331: Updated help on media caption field text (#542)
- Allow admins to change the home page via site settings (#540)
- D8CORE-5833: Fix for courses view to respect chosen limit in card grid list paragraph (#539)

8.x-3.1
--------------------------------------------------------------------------------
_Release Date: 2022-04-27_
- Locked migrate_plus version to 5.2. (#249)
- CS-79: Opportunities grid (#245)
- CS-79: Added opportunity grid component and styles.
- CS-79: Change CS opportunity view to use opportunity grid component and styled view.
- CS-79: Added and enabled smart_trim module.
- CS-79: Updated administer opportunities view.
- Updated circleci caravan version.
- Enabled transliterate_filenames module.

8.x-3.0
--------------------------------------------------------------------------------
_Release Date: 2022-04-08_
- D8CORE-5172: Updated references to localist and events-legacy urls
- DO not require lockup option select, prevent requiring lockup fields
- updated circleci and composer branches
- switch master branch to main
- Configure layout builder restrictions consistently (#509)
- Enable minimally branded theme for easier switching (#508)
- adjusted VBO dependency to inherit from stanford_actions
- Updated google analytics to latest 4.0 version
- D8CORE-3345 Updated path auto pattern for events, news, and people (#505)
- Added jsonapi_extras and disable write access
- D8CORE-4704 Fix person list to show nested content (#506)
- D8CORE-4526 Adjust full width layout for page title position (#497)
- D8CORE-5583 enabled views_custom_cache_tag module
- Process Localist html to fix <img> tag styles to attributes (#504)
- Removed scheduler from media and taxonomy terms
- conditional fields (#503)
- Added and enabled webp for performance improvement
- Modified the revision test to have a dynamic page title.
- Enabled pdb_react module.
- D8CORE-2893: Added minimally branded subtheme (#492)

8.x-2.9
--------------------------------------------------------------------------------
_Release Date: 2022-03-10_
- Updates from stanford_profile 2022-03-09
- Added and enabled webp for performance improvement
- Modified the revision test to have a dynamic page title.
- Enabled pdb_react module.
- D8CORE-2893: Added minimally branded subtheme (#492)
- D8CORE-5180 D8CORE-5227 Remove alt text on people images (#498)
- D8CORE-4713 Added id attribute for several wysiwyg tags (#496)
- D8CORE-4974 Added a third content block for the local footer (#491)
- BOT-8: Adjusted file upload access test for Intranet and allow_file_uploads. (#493)
- Updated config and tests for smartdate module update (#494)
- D8CORE-5278 Added scheduler module and configured for all content types (#486)
- preg_replace of null is deprecated in php 8, use strings (#490)
- Fix pathauto parent path generation (#489)
- D8CORE-5236: Updated text on "Load More" buttons to be more descriptive (#483)

8.x-2.8
--------------------------------------------------------------------------------
_Release Date: 2022-03-03_
- Updates from stanford_profile 2022-03-03
- D8CORE-5236: Updated text on "Load More" buttons to be more descriptive (#483)
- D8CORE-5000: missed the profile module change
- Fixed search API indexing
- Fix pathauto parent path generation (#489)
- preg_replace of null is deprecated in php 8, use strings (#490)
- D8CORE-5278 Added scheduler module and configured for all content types (#486)
- Updated config and tests for smartdate module update (#494)
- BOT-8: Adjusted file upload access test for Intranet and allow_file_uploads. (#493)
- Adjusted stanford_profile dependency branch to include code to be tested.
- D8CORE-4974 Added a third content block for the local footer (#491)
- D8CORE-4713 Added id attribute for several wysiwyg tags (#496)
- D8CORE-5180 D8CORE-5227 Remove alt text on people images (#498)
- Updated scheduler composer constraint to match stanford_profile upstream.

8.x-2.6
--------------------------------------------------------------------------------
_Release Date: 2022-01-28_
- Fixed the visibility conditions for the search block (#482)
- D8CORE-5398 Rename Legacy events importer field (#481)
- Added administrative file list and delete actions (#480)
- Fixed the visibility conditions for the search block (#482)
- D8CORE-5398 Rename Legacy events importer field (#481)
- Added administrative file list and delete actions (#480)
- D8CORE-5221 D8CORE-4857 D8CORE-3517 Small adjustments to configs and content. (#476)
- Add more steps to the site search disable tests (#478)
- D8CORE-5193 Configure localist importer to forget about old events (#475)
- Merge branch 'master' into 8.x-2.x
- Added localist url to external source field (#471)
- Adjust localist end dates for Smart Date "All Day" (#474)
- Added checkbox to hide site search in site settings config page (#472)
- D8CORE-5183: changes to external links config to skip localist (#473)
- D8CORE-5119: added help text for the localist import field. (#470)
- Upgrade drupal core to 9.3.0 with config updates (#469)

8.x-2.4
--------------------------------------------------------------------------------
_Release Date: 2021-09-21_

- Updates from stanford_profile 2021-09-21
- D8CORE-4534: adding a skip anchor block to the filtered pub page (#437)
- D8CORE-4643 Swapped out the "menu block" block with a regular "menu" block (#446)

8.x-2.4
--------------------------------------------------------------------------------
_Release Date: 2021-08-12_

- TASK00241678 Add limited role for only opportunities
- TASK00241687 Adjust font size on filters

8.x-2.3
--------------------------------------------------------------------------------
_Release Date: 2021-08-11_

- TASK00242878 Fixed search results for opportunity content
- D8CORE-4653 Add editoria11y module with permissions for contributors (#445)
- D8CORE-4681 Enabled rabbit hole taxonomy support (#443)
- D8CORE-4350 Sort publications by month and day along with year (#444)
- CAW21-82 Build accordion paragraph component (#440)
- D8CORE-2277: updated fields help text (#441)
- D8CORE-2278: Tweaks to Event Series help text (#442)
- D8CORE-4668 Allow new google analytics account id format `G-` (#439)
- Updates from stanford_profile 2021-07-21
- fixed html yml config
- Adjusted mathjax module CDN url
- D8CORE-2594 Allow menu placement under unpublished pages (#435)
- D8CORE-4497 Replaced <h3> with <p> in views no results text
- D8CORE-4235 Require alt text on gallery images (#433)
- D8CORE-4504 Added mathjax for latex formula support (#434)
- D8CORE-4194 Add url text for the link on media w caption paragraph (#423)
- Added and enabled the PDB module (#432)
- Updates from Stanford Profile 2021-06-28
- D8CORE-4378: adding the skip to main content on these list page layouts that were missing (#431)
- Corrected the events schedule display settings for the pattern refactor
- Exported configs after Drupal 9.2.0 upgrade (#429)

8.x-2.2
--------------------------------------------------------------------------------
_Release Date: 2021-06-14_

- Updates from stanford_profile.
- Disabled and replace global search block.

8.x-2.1
--------------------------------------------------------------------------------
_Release Date: 2021-06-11_

- Updates from stanford_profile.
- Include all content types in the XML sitemap.

8.x-2.0
--------------------------------------------------------------------------------
_Release Date: 2021-06-08_

- Updates from stanford_profile.

8.x-1.12
--------------------------------------------------------------------------------
_Release Date: 2021-02-22_

- Fixed migrate process plugin for time comparison skip.

8.x-1.11
--------------------------------------------------------------------------------
_Release Date: 2021-02-17_

- Migration Process Plugin to skip on time comparison (#159)
- Stanford profile updates 2020-12-09
- Dont remove special characters
- D8CORE-2431: configs (#333)
- D8CORE-2867 Update image metadata tags (#339)
- D8CORE-2325: fixed separator between event dates (#338)
- D8CORE-000 Patch simplesamlphp auth module to prevent unwanted redirect (#337)
- D8CORE-3051 Allow paragraph views to display nested taxonomy content (#335)
- Updated default content module (#336)
- D8CORE-2878 Add coalesce support for small animated gif fix (#334)
- D8CORE-2759 Fix revisions table to show all revisions (#296)
- D8CORE-2957 Updated help section language (#330)
- D9 Ready (#329)
- D8CORE-2495 Changed the link button in minimal html format (#332)
- Stanford profile updates 2020-11-19
- Merge pull request #331 from SU-SWS/hotfix-SA-CORE-2020-012

8.x-1.10
--------------------------------------------------------------------------------
_Release Date: 2020-11-09_

- Updates from stanford_profile
- Adjusted external link icon position.

8.x-1.9
--------------------------------------------------------------------------------
_Release Date: 2020-10-14_

- Adjusted solo importer to import images correctly.
- Adjusted font sizes.

8.x-1.8
--------------------------------------------------------------------------------
_Release Date: 2020-10-06_

- Updates from stanford_profile 2020-10-06


8.x-1.7
--------------------------------------------------------------------------------
_Release Date: 2020-09-25_

- Fixed react filters to encode the query string.

8.x-1.6
--------------------------------------------------------------------------------
_Release Date: 2020-09-24_

- Added favorites to resource pages
- Added summary field to opportunities
- Link and wysiwyg style fixes

8.x-1.5
--------------------------------------------------------------------------------
_Release Date: 2020-09-16_

- CSD-476 Hide deadline on event opportunities
- CSD-477 Removed duration block and unlink sponsor terms (#134)

8.x-1.4
--------------------------------------------------------------------------------
_Release Date: 2020-09-15_

- Initial Release
