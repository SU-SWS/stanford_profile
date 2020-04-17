# Stanford Profile
8.x-1.7
--------------------------------------------------------------------------------
_Release Date: 2020-04-17_

- D8CORE-1847 fix link text getting encoded (#179)

8.x-1.6
--------------------------------------------------------------------------------
_Release Date: 2020-04-17_

- Make edit link clicking more specific (#157)
- D8CORE-1452: update line lengths (#158)
- fixed seckit split (#161)
- D8CORE-1789 Fix xmlsitemap 404 response (#162)
- D8CORE-1708 D8CORE-1707: Disable menu items in toolbar. (#165)
- Set up codeception tests framework (#153)
- D8CORE-1792: Enabled a11y_checker for text editor. (#166)
- D8CORE-1664 Move to a release branch workflow with dev versions (#167)
- D8CORE-1816 wysiwyg tweaks (#175)
- D8CORE-1501 Added drush command to generate stress test node (#173)
- Dependency config update (#170)
- D8CORE-1197 Added google form media type (#169)
- D8CORE-1497 D8CORE-1681 Added spacer paragraph type and enabled multiple per row (#159)
- D8CORE-1499: Multiple Per Row (#176)

8.x-1.5
--------------------------------------------------------------------------------
_Release Date: 2020-03-20_

- Added step in behat tests to wait for modal to be gone (#155)

8.x-1.4
--------------------------------------------------------------------------------
_Release Date: 2020-03-20_

- Bumped version of `views_bulk_operations` to 3.6 (#137)
- D8CORE-1471: Allow site_manager to assign the site_manager role. (#138)
- Changed default workgroup for saml role mapping to uit:sws so Marco doesn't get admin. (#140)
- D8CORE-1405: Added alt text to default content. (#141)
- Created a config split for acsf modules. (#131)
- D8CORE-1547: Check for node instance. (#145)
- D8CORE-1351: Add Paranoia module. (#143)
- Updated behat test to use existing step definition "Then I click" (fixes bug in tests)
- D8CORE-1401: wysiwyg tweaks. (#144)
- D8CORE-943: Added more image styles for wysiwyg. (#146)
- D8CORE-1689: Allow custom blocks in layout builder. (#149)
- D8CORE-1659: Set image metatags to use an image style. (#148)
- D8CORE-1514: Add administer media permission to site managers. (#150)
- Changed stanford_page_layout_full layout option to only be on stanford pages. (#151)


8.x-1.3
--------------------------------------------------------------------------------
_Release Date: 2020-02-27_

- D8CORE-1307: Full width page only stylesheet. (#118)
- D8CORE-1357: Adds validation to config form uri. (#112)
- D8CORE-1282: Invalidate node page cache when new menu items are added as children. (#119)
- D8CORE-1340 Use core link in wysiwyg for now (#120)
- D8CORE-1451: Do not limit url characters in local footer (#121)
- D8CORE-1370: Alter menu form and status messages for config_readonly users (#114)
- D8CORE-1327: Change filter-format to allow for html entities. (#123)
- Fixed drupal/paragraphs version (#117)
- Set home link to the home node for default content (#124)
- D8CORE-1409: Enable new filter in the full html format. (#125)
- D8CORE-1279: Fix for subtitle/subhead styles (#110)
- Disable nobots via state on site install (#128)
- D8CORE-1464: Kill margin-bottom on <p> tag (#130)

8.x-1.2
--------------------------------------------------------------------------------
_Release Date: 2020-02-21_

- Resynced the media library view with the drupaDl core version (#105)
- D8CORE-1393 Dont display page title block on 404 and 403 pages (#106)
- D8CORE-106: Add Media with Caption paragraph. (#59)
- D8CORE-1363: Dont show contextual links to users. (#108)
- D8CORE-1262: Change "Related Text" to "Card Text". (#108)
- D8CORE-1365: Moved banner to top banner field. (#108)
- D8CORE-1394: Allow <br> tags. (#108)
- D8CORE-1224: Add help text to layout selection field. (#108)
- D8CORE-1326: Allow sitemanagers to view all unpublished content. (#108)
- D8CORE-1313: Moving the ol and ul styles to the wysiwyg (#111)
- Added stanford media library to node form since react doesnt add it (#113)


8.x-1.1
--------------------------------------------------------------------------------
_Release Date: 2020-02-14_

- Happy Valentines Day!
- D8CORE-1177: WYSIWYG heading text alignment (#87)
- D8CORE-1234: Add missing breakpoint for 2:1 card responsive image style
- D8CORE-1261: Adjust content/nav gap. (#89)
- Added page title for non-node pages (#92)
- D8CORE-1254: Text alignment for lists (#95)
- Added field_formatter_class module (#93)
- D8CORE-1289: Update homepage banner default content (#96)
- D8CORE-369: Enable metatags and XMLSitemap on prod (#88)
- D8CORE-1114: Alter Node Edit Form for react_paragraphs (#97)
- D8CORE-1201 use confirm_leave module (#99)
- D8CORE-1007 Add image for default content (#100)
- Added github template files.
- Uninstall better_normalizers module
- Uninstall config_distro module
- Updated default content and media library images.
- D8CORE-1007: Add image for default content (#100)
- D8CORE-1019: Modified the full width layout (#102)

8.x-1.0
--------------------------------------------------------------------------------
_Release Date: 2020-02-05_

- Stable release!
- Many configuration updates
- Many style updates
- Many new tests, both unit and behat.
- D8CORE-938: Menu tweaks for primary and seconday navigation
- D8CORE-1153: Removed H6 from stanford_text formats
- D8CORE-1112: Page Authoring: style banner top
- D8CORE-971: fix home and no 2nd menu
- D8CORE-941: WYSIWYG Typography update and CKEditor styles
- D8CORE-1175: Provide additional help information
- D8CORE-931: Add anchor location for skip links to content layouts
- D8CORE-1117: Switch ot React Paragraphs
- D8CORE-1177: Override WYSIWYG text paragraph styles
- D8CORE-1221: Dark paragraph type icons
- D8CORE-1018: Adjust content and media menu item titles
- D8CORE-1243: SAML role mapping config page
- D8CORE-1028: line length fix
- Removed stanford_profile_install from being installed as it has nothing to do.

8.x-1.0-alpha5
--------------------------------------------------------------------------------
_Release Date: 2020-01-23_

- D8CORE-1200: Prevent home page from being deleted (#68)
- D8CORE-1013: Allow classes on header elements (#67)
- Fix for focal point config mis-match and failed circle ci behat tests.

8.x-1.0-alpha4
--------------------------------------------------------------------------------
_Release Date: 2020-01-22_

- D8CORE-970: adding wysiwyg scss file (#60)
- D8CORE-850 Added Ckeditor Sticky Toolbar (#51)
- Changed the widget for the media library form mode on images (#56)
- Adjusted some wysiwyg styles.

8.x-1.0-alpha3
--------------------------------------------------------------------------------
_Release Date: 2019-12-17_

- Layout library permissions
- UI language changes
- Prepare for sub-profiles
- Changed dependencies to install
- Add files from stanford_module_example
- Local footer config pages and block
- Fixed composer versions
- Use SNOW API to set site name users
- Paragraphs edit module for contextual links
- Move toolbar user tab to far right
- Allow videos in cards
- Add seckit module and config files; install with profile.
- Make card paragraph image 2:1 ratio
- Remove Environment Indicator permissions from roles

8.x-1.0-alpha2
--------------------------------------------------------------------------------
_Release Date: 2019-11-13_

- Require new modules: field_validation, layout_builder_restrictions
- Updated version requirement for pathauto
- New basic site settings for google google_analytics
- Changed default layouts for stanford_page to new jumpstart_ui dynamic layouts
- Enabled modules: field_validation, google_analytics, layout_builder_restrictions, path_alias
- Updated primary and secondary navigation settings.

8.x-1.0-alpha1
--------------------------------------------------------------------------------
_Release Date: 2019-10-30_

- Initial Release for Pilot Projects
