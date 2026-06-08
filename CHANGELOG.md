# Stanford Profile

12.2.3
-------------------------------------------------------------------------------
- Created barebones theme Jemison (#1089)
- D8CORE-8721: Added a comma between fields (#1088)
- Upgrade drupal core to 11.3 (#1047)
- Added hit item click event for algolia analytics
- Exclude items from search indexing if they are in the trash

12.2.2
-------------------------------------------------------------------------------
- D8CORE-8658: Update aria-label for secondary navigation

12.2.1
-------------------------------------------------------------------------------
- Check for decoupled menu item access for user

12.2.0
-------------------------------------------------------------------------------
- Ignore PDFs for Editoria11y so we don't trigger WAF (#1068)
- Allow anchor link navigation block (#1043)
- Removed short title from the Person Node (#1062)
- Fix problem with Courses Mgmt view (#1037)
- Updating styling for secondary navigation (#1046)
- Update spotlight title to use h1 and filter tags to list (#1042)

12.1.2
-------------------------------------------------------------------------------
- Updated help text on news dek field
- Added localist id field for lookup on the external system

12.1.1
-------------------------------------------------------------------------------
- Add course_filters to site manager role permissions
- Increase number of admin toolbar entity bundles available

12.1.0
-------------------------------------------------------------------------------
- Remove alt text for people image cards
- Add course filtering view (#1028)
- New People filtered list view and adjustments to edit form and help text (#1025)
- D8CORE-8462 D8CORE-8463 | add spotlight filters to news card display (#1027)
- update spotlight list paragraph grid from 4 to 3 (#1026)
- Moved the list class names on the view (#1020)
- Added new media content type, taxonomy, and views. (#1004)
- News adjust label of the title field (#1023)
- replace spotlight text in layout with content block; show body field on Spotlight variant (#1022)
- Removed link from spotlight filter terms in H3 Card display mode (#1021)
- smart trim spotlight quotes in card displays (#1019)
- Removed default "Spotlights" taxonomy term
- Style spotlight card images as 1/1 square (#1017)
- D8CORE-8234 Spotlights-related Views for paragraphs (#1015)
- Added related spotlights view display (#1013)
- Hide and show news fields correctly based on variants (#1012)
- Add body field to spotlight node display (#1016)
- Course Filters field (#1007)
- added no follow to link to person node (#1014)
- Use display mode instead of fields in event views (#1011)
- changed permissions on sunet field in site settings (#1009)
- Adjust date on RSS feeds (#1003)
- Build out spotlight variant on News CT (#1005)
- Removing fields from Opportunity content type (#1001)
- Add counts and other tools to manage content views (#997)
- Increase news dek field from 180 to 500 characters (#998)
- D8CORE-8316 | Adjust events page details section layout (#996)
- SUL23-818 | Update font awesome icon help text (#994)
- STVP26-74 STVP26-75: Add "Body" field to news and basic pages (#993)
- D8CORE-8196 | update card structure to match banner and fulfill a11y (#992)
- D8CORE-8196 | adjust banner overlay card content order for a11y (#990)


12.0.2
-------------------------------------------------------------------------------

- Updated external link settings
- Added acceptance test for publication csv importer
- Prevent federated search checkbox shrinking
- Added person and policy external source fields to graphql

12.0.2
-------------------------------------------------------------------------------
- Fix label display when fields are empty (#985)
- Adjust opportunity form display field order

12.0.1
-------------------------------------------------------------------------------

- A11y changes for federated search (#982)
- Set title on profile helper cron job


12.0.0
-------------------------------------------------------------------------------

- Remove response_code_condition (#978)
- adding events, person, and publication filter tag fields (#969)
- D8CORE-7937 Remove "Bright" brand bar option
- Adding sponsors to the Opportunity node (#965)
- Remove jsonapi endpoint for users (#974)
- RSS Feeds for Events (#972)
- Added ext link icons to Utility nav (#967)
- Added external source field and functionality to policy content type (#971)
- Upgrade everything to Drupal 11 (#968)
- D8CORE-7586 Expand algolia to allow for federated search feature (#966)
- Updated filtered opportunities GQL view settings
- Fixed typo in site org taxonomy term

11.8.3
-------------------------------------------------------------------------------
- Fixed minimally branded theme unpublished banner visibility
- Update book config

11.8.2
-------------------------------------------------------------------------------
- D8CORE-8052 Style social link for "X.com" to match "twitter.com"
- Upgrade to drupal 10.5

11.8.1
-------------------------------------------------------------------------------
- UE-536: adjusting opportunity layout to be in only 2 cols (#956)
- Wrap page header and page footer in `data-nosnippet` to prevent google snippets from the menus
- Removed duplication of fields in sidebar on opportunities

11.8.0
-------------------------------------------------------------------------------
- Move opportunity fields into a view for better styles (#924)
- D8CORE-8063 Fix html structure on opportunity list items (#952)
- D8CORE-8071 Added an "Imported" field populated by importers (#951)
- D8CORE-8014 Fix hover state on mobile menu hamburger
- D8CORE-8065 Move opportunity node page "Main content" anchor above page title
- Adjust opportunity filters to use radio buttons (#950)
- RSS Feed For News (#936)
- Added heading level selection for stat card paragraph (#949)
- Adding spacing to header bottom when there is no navigation menu. (#943)
- Add fontawesome icon template to hide icon from screen readers
- Copy the utility links and buttons for the mobile display (#935)
- Changed sort for the opportunities view (#945)
- Update stat card color choices
- Updated editorially settings
- Require headline in stat card (#940)
- Added stat card icon
- Add validation for numeric "Items to display" on list paragraph (#937)
- D8CORE-7843 D8CORE-8086 Add stat card and section background color options (#932)
- D8CORE-8042 - External source field for person with page redirect (#933)
- Add unpublished site banner to all pages (#931)
- added past and upcoming filter and indicator to "Manage events" page. (#928)
- Manage Basic Pages content management view (#927)
- Update Event Importer help text (#929)

11.7.0
-------------------------------------------------------------------------------
- D8CORE-8031 Force displaying both regions on opportunity  layout
- made the contact email a link (#921)
- Add fallback sorting to graphql views
- Fixed course card grid to display as a grid, instead of list
- Exclude external link icon from news teasers due to template implementation
- D8CORE-8024 Fix order of decoupled menu items (#919)
- D8CORE-8025 fixed tabing order and spacing on the header and utility nav (#920)
- Opportunities header items changes for alignment when no icon (#918)
- Expose entity ids in graphql to allow for view filtering'
- Utility navigation styles (#911)
- FEATURE: Opportunities content type enhanced with node display, views and filterable views. (#883)
- Updated edit form for Site Header Options (#916)
- D8CORE-7924 Add header utility links to site settings (#900)
- Remove unwanted fields from search indexing displays
- Updated decoupled menu to correctly display sub menu items
- Fixed full width basic page title position (#891)
- Update trash module config

11.6.2
-------------------------------------------------------------------------------
- Fixed logic error in main menu when home page is a node path.

11.6.1
-------------------------------------------------------------------------------
- Improve reliability of acceptance tests.

11.6.0
-------------------------------------------------------------------------------
- D8CORE-7292 - Updated the help pages. (#864)
- Traverse entire main menu to add appropriate unpublished indicators
- D8CORE-7835 Fix accordion expand/collapse all behavior to not behave like a toggle
- Update field Validation plugins to new constraints (#879)
- Modify field validation on social link field to improve error display.
- Changing the color of headings in wysiwyg for Minimal subtheme. (#872)
- Allow unpublishing of taxonomy terms (#874)
- Change Character Limit for Accordion (#875)
- Added stage file proxy for dev, stage, and local
- Implement shield on dev and stage (#873)
- D8CORE-7737 D8CORE-7738 D8CORE-7824 Install state changes (#871)
- Configure trash module with necessary test changes (#852)
- reorganized the opportunity node page (#833)
- fixed the text color in the more publications button (#870)

11.5.3
-------------------------------------------------------------------------------
- Added site manager permission to grant site reviewer role.

11.5.2
-------------------------------------------------------------------------------
- Removed non-existent permissions

11.5.1
-------------------------------------------------------------------------------
- D8CORE 7753 refactor accordion paragraph to use button and div elements (#863)
- Fix typo in lockup config page settings. (#862)
- Self-Service Enhanced Search (#861)
- Content Management pages for person, event and news (#858)
- Site Reviewer Role (#855)
- added net to the local footer social link validation (#859)
- updated user creation mail (#856)
- h1 on Homepage (#847)
- added help text and additional icons to social links in footer. (#846)
- Revert "Add and configure trash module for soft deleteing content (#835)" (#851)
- Update tests to work correctly with trash module (#850)
- removed max-width on intro text and drop cap text (#843)
- fixed bad link, updated text (#844)
- removed wysiwyg p line length (#842)
- Add and configure trash module for soft deleteing content (#835)
- removed the medium and small quote (#840)
- Updated editora11y module config (#841)
- Fix tests with profile helper module dev (#839)
- Remove obsolete module block_content_permissions (#838)
- Drupal 11.4 update
- Change to the Quote style in the wysiwyg
- Fixup citation entity config export
- Updated environment indicator settings after update
- Update linkit module (#834)
- add gutters to accordions on full width
- Adjust skip secondary nav anchor link
- allow all language options in ckeditor
- Update ultimate cron config after module update (#832)
- Updated config from db update

11.5.0
-------------------------------------------------------------------------------
- Updated metatag module config
- Exposed opportunity in graphql
- D8CORE-7194 Embed codes with captions respect 100% widths on iframes
- D8CORE-6644 Omit default favicons if one is uploaded in the theme settings
- D8CORE-5828 If home page starts with a wysiwyg, add some space at the top
- Added aria label to masthead section
- D8CORE-6821 Adjust skip to main content location
- Move back-to-top inside the <main> element
- D8CORE-7434 D8CORE-7435 D8CORE-7436 | Opportunity full content layout and configure card and list display (#813)
- D8CORE-7578 Adjust person display for lists and improve resolution (#828)
- added the global footer to minimal theme in dark with no logo (#820)
- Added FAQ Accordion List paragraph type based on CAW (#827)
- Added modal colorbox image display choices in the wysiwyg (#826)

11.4.5
-------------------------------------------------------------------------------
- Remove edit links in views (#821)
- removed decanter link and replaced with identity guide (#822)
- D8CORE-7517 | update event subtitle heading (#816)
- adding a few additional "Site Type" options

11.4.4
-------------------------------------------------------------------------------
- Updated image_effects module for compatibility with imagemagic 4.0.0.

11.4.3
-------------------------------------------------------------------------------
- Fixed basic page cards when the page has a "page title banner" image.

11.4.2
-------------------------------------------------------------------------------
- Adjusted codeception test for cache issue.

11.4.1
-------------------------------------------------------------------------------
- Added "Site Type" field to site information config page.

11.4.0
-------------------------------------------------------------------------------
- Enabled internal link force relative option
- D8CORE-7424 Added various HTML attributes to the WSYIWYG allowed list (#799)
- Opportunities Content type with small amount of styles (#801)
- Updated decoupled menu and algolia search components
- D8CORE-7455 Algolia Search: Decode html entities before stripping the html
- D8CORE-7422 Write test for basic page list paragraph display sorts (#798)
- D8CORE-7400 Remove alt attribute on news card images
- D8CORE-7418 Require course link url field
- D8CORE-7429 Display event subheadline as normal text instead of h2
- D8CORE-7425 Display dek and date field on news cards
- Update to drupal 10.3.0 (#794)
- Updated permission for config pages and decoupled user role
- Enable basic auth module and remove permissions for graphql
- D8CORE-7413 Enabled search api processor to add spaces between html tags (#792)
- Different sorting option for Basic Page lists (#791)

11.3.8
-------------------------------------------------------------------------------

- Updated stanford_samlauth.settings timeout.

11.3.7
-------------------------------------------------------------------------------
- Improve decoupled menu by using js data on the page instead of json api
- D8CORE-7362 Add necessary permission for site embedder role (#786)
- Fixed publication url rabbit hole
- Fix lockup on very small screen size (#785)
- Added noscript on algolia search results page
- D8CORE-7360 Adjust Person  node detail page heading structure for "Links" field

11.3.6
-------------------------------------------------------------------------------
- D8CORE-000 Update Past Event List

11.3.5
-------------------------------------------------------------------------------
- Disabled link trimming on content type displays and view settings.

11.3.4
-------------------------------------------------------------------------------
- Index algolia items only during cron
- Added event image to algolia index data
- D8CORE-7295 D8CORE-7301 Enable rabbit hole for publications content type

11.3.3
-------------------------------------------------------------------------------
- D8CORE-7341 Add h2 headings to courses quarter term pages

11.3.2
-------------------------------------------------------------------------------
- D8CORE-7341 Add h2 headings to past events and courses term pages

11.3.1
-------------------------------------------------------------------------------
- Fix images and oembed videos to be lazy loading

11.3.0
-------------------------------------------------------------------------------
- Add taxonomy terms to algolia search indexing
- D8CORE-7333 Change paragraph behaviors for teaser and lists (#774)
- Add role evaluation choice to saml config page
- D8CORE-7307 Remove anonymous access to /patterns or /admin/patterns page
- added test for stacking site_embedder role (#773)
- D8CORE-7331 Use aria-labelledby on the submenu buttons in the decoupled menu
- D8CORE-7300 Add Algolia checkbox to allow indexing separate from search results
- D8CORE-7212 Use h3 header for cards in teasers when appropriate (#765)
- D8CORE-7207 Add Page Title Banner Paragraph (#754)
- D8CORE-7211 Header behaviors for card and banners (#763)
- added pager for events and publication graphql views

11.2.1
-------------------------------------------------------------------------------
- Provide items per page for graphql views
- Expose event image via graphql
- Added external image url for events content from Localist

11.2.1
-------------------------------------------------------------------------------
- Fixed logger syntax during site install.

11.2.0
-------------------------------------------------------------------------------
- Removed decanter from stanford_basic in favor of jumpstart_ui templates
- D8CORE-7125 Use field_label module to choose which heading element for field labels (#761)
- D8CORE-6982 Add validation to publication DOI fields to avoid full urls
- D8CORE-7203 Some improvements to metatags on some content types
- Add empty alt text to logo in policy print page
- D8CORE-7088 Replace policy print page h2 with h1 tag
- Adjust allowed menus in layout builder restrictions
- D8CORE-7132 Add policy content types to teasser paragraph field
- D8CORE-7255 exclude lazy load oembed links from external links (#760)
- D8CORE-7215 Test to Display the topic menu label on list pages (#758)
- Added "fields" to page cache query ignore for jsonapi
- D8CORE-7111 Add local footer social icons for mastodon and google scholar (#756)
- Increase jsonapi page limit to 500 for nodes
- D8CORE-7205 Trim all html before sending index data to Algolia
- Replaced transliterate_filenames module functionality
- Add and configure graphql compose module (#751)
- Drupal 11.2.0 upgrade (#752)

11.1.0
-------------------------------------------------------------------------------
- D8CORE-7096 Update algolia search results styles and structure (#747)
- D8CORE-7074 Implement Search API Algolia (#743)
- Overrode the colorbox formatter template to remove aria-label (#746)
- Update alert styles (#744)
- reduced padding on filterby menu (#728)

11.0.8
-------------------------------------------------------------------------------
- D8CORE-7066 Adjust site contact fields & language (#741)
- Update contact field labels
- Adjusted access denied message
- Added the anchor for contact details on site settings

11.0.7
-------------------------------------------------------------------------------
- Invalidate cache tag when saving site contact details
- Allow figure and figcaption in embeddables

11.0.6
-------------------------------------------------------------------------------
- Fixed event day display with timezones.

11.0.4
-------------------------------------------------------------------------------
- D8CORE-7051 Use display field copy instead of token fields

11.0.3
-------------------------------------------------------------------------------
- Next site settings and config ignores (#735)
- D8CORE-7040 Fix source ending tags

11.0.2
-------------------------------------------------------------------------------
- Fix acceptance tests.

11.0.1
-------------------------------------------------------------------------------
- Fix site setting redirect logic

11.0.0
-------------------------------------------------------------------------------
- Use h2 heading for global message
- updated to newest decanter (#730)
- Update config ignore settings for latest module (#729)
- Update local footer config userguide links (#727)
- Added and configured autoprefixer for css compiler
- Updated search api config
- Fixed related policies to avoid self referencing
- SDSS-1007: Add support for global footer variant. (#724)
- Added su-masthead-inner class to masthead <section>. (#725)
- updates for new Decanter and updating webpack (#710)
- D8CORE-6951  D10 Clean up admin toolbar (#723)
- changed to list style none (#719)
- Additional edits to the editing buttons (#722)
- Display field copy released a D10 version
- Lifecycle management contact fields
- fixes to local task tabs (#720)
- D8CORE-6977 Switch to use CSHS instead of simple heiracry select (#721)
- D8CORE-6984 include policy content in sitemap
- D8CORE-3718 Minor a11y issues for decoupled main menu
- Updated user mail attribute value for samlauth
- Drupal 10 Config & test updates (#705)
- D10 Install

10.1.1
-------------------------------------------------------------------------------
- Update samlauth settings when a role is created or deleted

10.1.0
-------------------------------------------------------------------------------
- Use components field instead of layout builder for list pages (#709)
- updated permissions for contributors and site editors (#690)
- Update bad user guide links in help text (#694)
- Fixed styles for login page when on intranets
- changed the non discrimination link (#707)
- D8CORE-6842 Added and configured stanford_samlauth (#701)
- D8CORE-6844 Fixup mobile menu at medium breakpoint (#703)
- Update localist importer to use the localist json data parser
- Updating to the new font through a Decanter update (#695)
- Added and configured Oembed lazyload for video media
- D8CORE-4495 Update past events text on node view
- Updated publication importer for layout paragraphs instead of react paragraphs


10.0.4
-------------------------------------------------------------------------------
- Update hook to update field storage definitions.

10.0.3
-------------------------------------------------------------------------------
- Update decoupled menu to recognize "Expanded" menu setting
- D8CORE-6816 Restored permissions to run importers for site managers
- Add missing chosen library
- Updated google tag settings

10.0.2
-------------------------------------------------------------------------------
- Added to the update hook to handle layout builder menu blocks when updating menu links.
- Added user json api endpoint.

10.0.1
--------------------------------------------------------------------------------
- add ptype classes just in case
- Remove unwanted paragraph types on publication field
- Add new layout paragraphs styles class

10.0.0
--------------------------------------------------------------------------------

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
- Fixed typo in "Experimental"
- D8CORE-6770 Change lables from "Components" to "Paragraphs"
- Added "Admin notes" to the site settings config page
- D8CORE-6457 D8CORE-6476 D8CORE-6477 Tweaks to policy content fields
- Added undo and redo buttons to ckeditor
- Move help section to be below brand bar in header (#674)
- Revert "changing past events text (#669)" (#673)
- changing past events text (#669)
- D8CORE-5407 | @jdwjdwjdw | A11y: Update lockup cell2 max-width, line3 line-height (#672)
- Provide a new replacement menu with a decoupled main menu (#663)
- D8CORE-6416 Update google analytics tracking for stanford_basic theme
- D8CORE-2622, D8CORE-4494 | Move brand bar and skip-links into header landmark banner (#665)
- D8CORE-6654 | Update stanford_basic package-lock (#667)
- Added tiny base64 blur image to json api data
- changing hover on print icon for policy CT (#661)
- Updated jsonapi endpoints (#664)
- Migrate everything to layout paragraphs and upgrade all modules/themes (#654)


9.2.8
--------------------------------------------------------------------------------
- Don't replace attributes on menu link items.

9.2.7
--------------------------------------------------------------------------------
- Moved the help region below the brand bar.

9.2.6
--------------------------------------------------------------------------------
- Exported configs after db updates
- D8CORE-6416 Update google analytics tracking for stanford_basic theme
- Added better tag and release action
- Fix policy typo (#662)
- D8CORE-2622, D8CORE-4494 Move brand bar and skip-links into header landmark banner (#665)
- D8CORE-6654 Update stanford_basic package-lock (#667)
- changing hover on print icon for policy CT (#661)
- Updated permissions to allow site managers create media taxonomy

9.2.5
--------------------------------------------------------------------------------
- Replace log entity type and remove ECK (#656)
- Enabled empty_fields module
- DEVOPS-000: SMRT quotes R dumb (#655)

9.2.4
--------------------------------------------------------------------------------
_Release Date: 2022-12-13_
- Added missing drupal/jsonapi_hypermedia module
- Fixed related policies display

9.2.3
--------------------------------------------------------------------------------
_Release Date: 2022-12-03_
- Hotfix. Fixup composer.json

9.2.2
--------------------------------------------------------------------------------
_Release Date: 2022-12-02_
- Hotfix. Remove memcache

9.2.1
--------------------------------------------------------------------------------
_Release Date: 2022-11-30_
- D8CORE-6424 Hide legacy importer fields on importer form
- D8CORE-6338 updated help text on policy changelog fields
- D8CORE-6422 Allow news to hide social share icons
- Moved the authority field. (#647)
- Improved tests for configuration ignore
- Fix preprocess_breadcrumbs to prevent failure with drush
- Add and enable memcache for dev, stage, and prod (#645) (remove?)
- D8CORE-2932 and Fixed extra spacing on people list items. (#641)
- adding summary into the related policy cards (#640)
- Display policy change log title in the lists (#642)
- Added and configured "Page Cache Query Ignore" module

9.2.0
--------------------------------------------------------------------------------
_Release Date: 2022-10-25_

- D8CORE-6347 Show body summary and add help text
- D8CORE-6346 Reorder form fields and add help text
- Added and adjusted printable view mode for policies
- Prepend "Canceled" to canceled events
- Corrected courses migration (#638)
- moved the back and forward button for mobile (#636)
- Moved modules into consolidated repo location for stanford_profile_helper (#637)
- Added update hook to make deployment smoother
- unique ids for prev/next buttons (#635)
- fix to the policy related cards for mobile (#634)
- adding styling for change log block anywhere (#633)
- D8CORE-6325 Set the active item on book side navigation (#632)
- Added additional contact information field for events (#631)
- layout and style set up for Policy (#628)
- Added a toggle in the theme to turn off the external links (#623)
- adding the logo for the print function on policy (#625)
- Added Fast 404 module (#630)
- D8CORE-6315 Remove duplicate "All" publication menu link
- Apply chosen to related policy field
- D8CORE-6312 Fix node title not reflecting the changes (#629)
- D8CORE-5250 Add "Last Updated" to search results items (#619)
- Modify directory permissions when copying the file during install (#626)
- Added view to display policy child pages (#624)
- D8CORE-6288 Policy content type (#617)
- D8CORE-6247 Add "Code" to WYSIWYG Style dropdown
- D8CORE-6242 D8CORE-4977 D8CORE-6055 Improve people lists and add pronoun field (#615)
- Disable confirm-leave js on CI environments
- fixing float with a  clear (#614)
- Sjpw images in cards at all breakpoint (#610)
- D8CORE-6235 Fix "Save and Import" on importer forms. (#613)
- D8CORE-6058 Adjust, improve, and add metatags for content (#608)
- D8CORE-6217 Allow configuring maximum main menu depth (#611)
- D8CORE-5825 Add taxonomy field to media types for categorization (#609)
- fix to the transparent localist event link (#612)
- D8CORE-6224 Added localist bookmark url for importer (#607)
- Added journal publisher field, updated publisher label. (#602)
- D8CORE-5656 D8CORE-6215 D8CORE-6048 Adjustments to people node form and views (#606)

9.1.3
--------------------------------------------------------------------------------
_Release Date: 2022-08-22_

- Fixed courses field widget with view query parameter.

9.1.2
--------------------------------------------------------------------------------
_Release Date: 2022-08-15_

- D8CORE-6219 Adjust help text and remove aria label help text
- D8CORE-6168 Default to 12 column widths on paragraph row items

9.1.1
--------------------------------------------------------------------------------
_Release Date: 2022-08-09_

- Updated field validation for google analytics and allow multiple property ids (#600)


9.1.0
--------------------------------------------------------------------------------
_Release Date: 2022-08-08_

- D8CORE-6006 Disable link attributes module (#597)
- Improve person importer url fetching with some error handeling
- Add functional tests for wysiwyg button focus (#593)
- Validate menu items for local absoluate urls (#594)
- Update StanfordTextEditorTest unit test(#595)
- D8CORE-5867 Simplify and unify rabbit hole message and action (#592)
- D8CORE-5975 Allow resetting taxonomy term order to alphabetical with the button (#591)
- Updated field encrypt module to version 3.0.0
- Updated configs for latest core and contrib modules
- Hide "Sticky" and "Promote" fields on node form
- Fix duplicates in course lists
- D8CORE-6035 Show image title field when uploading in media library.
- Ensure events importer widget works when the API is empty
- Removed entity_print from composer.json (#589)
- Underline buttons on the events mini calendar. (#586)
- Updated config from search_api module
- Added additional html elements to embeddables allow list (#584)
- fix up to alignment. (#569)
- D8CORE-6003 Save terms in the order they were chosen (#583)
- D8CORE-6005 Allow Span tags in the wysiwyg
- D8CORE-5128 Enable embed code validators (#579)
- Refactored and improved codeception tests.
- fixed composer namespace to lowercase
- Removed fzaninotto/faker workaround in CI tests
- removing the li from the ch line limit (#578)
- Removed obsolete checkbox from theme settings (#575)
- D8CORE-4972 Provide aria-label input for links on paragraphs (#573)
- removed unwanted composer files
- Updated drupal core 9.4
- Improve event sponsor field update hook (#574)
- D8CORE-3558 Fix error when the access token expires (#566)
- Move some CircleCi to GH Actions (#568)
- D8CORE-5860 Fix intranet icons for paragraphs and media
- D8CORE-4780 Changed search page button text to "Search"
- Updated event sponsor field "Add More" button label (#570)
- fixing font sizes within tables (#564)
- D8CORE-5598 making margins even on OL and UL (#565)
- D8CORE-5886 Enable ajax on people lists
- Added and configured ckeditor_blockimagepaste to prevent inline base64 images
- D8CORE-4858 Allow hiding paragraph and custom empty results message (#563)
- fixing the news alignment. (#559)
- changes to the font sizes in courses (#562)
- Adjusted VBO form for event date fields that are required
- D8CORE-4867 Publication lists on people pages (#561)
- D8CORe-5871 Change order of filter processing to fix <picture><source> tags
- D8CORE-5858 Add missing "All" courses menu item
- Locked citeprocphp to version 2.4.1, pre ext-intl requirement. (#560)
- D8CORE-5763 Updated default content (#558)
- D8CORE-5680 Switch list landing pages to nodes with layout builder settings (#552)
- Added edit buttons on courses list page (#555)
- Updated some codeception tests (#554)
- Added abbr buttons to ckeditor (#550)
- Disabled courses department importer
- Let editors sort content by author (#551)
- Added a second provider for Stanford University Library oEmbeds (#548)



9.0.1
--------------------------------------------------------------------------------
_Release Date: 2022-05-11_

- Updated submodules with their latest code form their repos.

9.0.0
--------------------------------------------------------------------------------
_Release Date: 2022-05-11_

- Consolidated dependency modules for easier development.


8.x-4.3
--------------------------------------------------------------------------------
_Release Date: 2022-05-11_

- Fixup for "Results For" on people list pages.


8.x-4.2
--------------------------------------------------------------------------------
_Release Date: 2022-05-10_

- Enabled aggregation for duplicates in course view

8.x-4.1
--------------------------------------------------------------------------------
_Release Date: 2022-05-10_

- Updated help on media caption field text (#542)
- Allow admins to change the home page via site settings (#540)
- Fix for courses view to respect chosen limit in card grid list paragraph (#539)

8.x-4.0
--------------------------------------------------------------------------------
_Release Date: 2022-05-02_

- Added twitter card metadata for person content
- Several tweaks to the taxonomy display and fields. (#532)
- Update block.block.minimally_branded_subtheme_pagetitle.yml (#535)
- Added custom block and edit link on `/courses` page (#534)
- Adding a listy style to the taxonomy terms (#533)
- adding the grid col 3 for three across (#530)
- added <object> and <param> to allowed tags in embeddables (#529)
- D8CORE-5729 People term pages: display only child terms groupings (#526)
- D8CORE-5187 Courses and Importer(#500)
- D8CORE-5611 Allow multiple basic page types and change widget
- D8CORE-5696 Only display location address once (#520)
- D8CORE-5629 Adjust profile link url for stanford only profiles (#521)
- D8CORE-4118 Remove layout builder settings on other display modes (#519)
- D8CORE-4128 Change views to HTML lists (#518)
- Enabled transliterate_filenames from stanford_media
- D8CORE-3975 Created shared tags vocabulary and fields (#511)
- D8CORE-5666 Enabled and configured responsive_tables_filter module (#515)
- Dont trim the url on even cards
- Updated link_title_formatter module
- Updated domain_301_redirect version
- Updated references to localist and events-legacy urls
- DO not require lockup option select, prevent requiring lockup fields


8.x-3.2
--------------------------------------------------------------------------------
_Release Date: 2022-03-22_

- DO not require lockup option select, prevent requiring lockup fields
- Updated references to localist and events-legacy urls

8.x-3.1
--------------------------------------------------------------------------------
_Release Date: 2022-03-22_

- Merge branch 'main' into 8.x


8.x-2.20
--------------------------------------------------------------------------------
_Release Date: 2022-03-17_

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

8.x-2.19
--------------------------------------------------------------------------------
_Release Date: 2022-03-08_

- Modified the revision test to have a dynamic page title.
- Enabled pdb_react module.
- Added minimally branded subtheme (#492)
- D8CORE-5180 D8CORE-5227 Remove alt text on people images (#498)
- D8CORE-4713 Added id attribute for several wysiwyg tags (#496)
- D8CORE-4974 Added a third content block for the local footer (#491)
- BOT-8: Adjusted file upload access test for Intranet and allow_file_uploads. (#493)
- Updated config and tests for smartdate module update (#494)
- D8CORE-5278 Added scheduler module and configured for all content types (#486)
- preg_replace of null is deprecated in php 8, use strings (#490)
- Fix pathauto parent path generation (#489)
- Updated text on "Load More" buttons to be more descriptive (#483)

8.x-2.18
--------------------------------------------------------------------------------
_Release Date: 2022-02-03_

- Adjusted events series main column width

8.x-2.17
--------------------------------------------------------------------------------
_Release Date: 2022-01-27_

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
- changes to external links config to skip localist (#473)
- added help text for the localist import field. (#470)
- Upgrade drupal core to 9.3.0 with config updates (#469)

8.x-2.16
--------------------------------------------------------------------------------
_Release Date: 2022-01-20_

- D8CORE-5266 Update events-legacy importer.


8.x-2.15
--------------------------------------------------------------------------------
_Release Date: 2021-11-19_

- D8CORE-4521 Localist Events Importer (#463)
- Change profile helper module weight for priority
- D8CORE-4677 Use an access token to fetch stanford-only profile images from CAP (#467)
- D8CORE-4860 Wrap publication importer body text in <p> (#466)
- D8CORE-4996 Remove taxonomy term on basic page card and list displays (#465)
- D8CORE-4246 Add fontawesome module for wysiwyg icon support (#464)
- D8CORE-4876 Enable ajax on publication list view
- D8CORE-4824 Disable accordion on event series
- Added configs for oEmbed Providers module (#462)
- adding a class to the table element and the aside element (#460)
- Merge branch 'master' into 8.x-2.x
- D8CORE-4816 Add configurable allowed tags for unstructured embed (#461)


8.x-2.14
--------------------------------------------------------------------------------
_Release Date: 2021-10-08_

- Added publications help text
- Index intranet content (#458)
- adjusted publication importer label
- Add citation to clonable entities
- tweaks to publication csv importer for easier data
- Added site manager permission to import publications
- Exported config after database updates with latest contrib
- D8CORE-3749 Publication CSV importer
- D8CORE-4693 Filter events by the second instead of the day (#457)
- D8CORE-4759 add specific view mode for search indexing for better control (#454)
- D8CORE-4096 Updated help section text (#455)
- D8CORE-3026 added and configured stanford_actions (#453)
- Added layout builder role (#451)
- Configure the new media embeddable validator (#450)
- Merge branch 'master' into 8.x-2.x
- adding a skip anchor block to the filtered pub page (#437)
- D8CORE-4749 Add and configure views bulk edit (#449)
- Merge branch 'master' into 8.x-2.x


8.x-2.12
--------------------------------------------------------------------------------
_Release Date: 2021-09-14_

- adding a skip anchor block to the filtered pub page (#437)

8.x-2.12
--------------------------------------------------------------------------------
_Release Date: 2021-09-09_

- Disable accordion for now.

8.x-2.11
--------------------------------------------------------------------------------
_Release Date: 2021-09-03_

- D8CORE-4643 Swapped out the "menu block" block with a regular "menu" block (#446)
- D8CORE-4653 Add editoria11y module with permissions for contributors (#445)
- D8CORE-4681 Enabled rabbit hole taxonomy support (#443)
- D8CORE-4350 Sort publications by month and day along with year (#444)
- CAW21-82 Build accordion paragraph component (#440)
- updated fields help text (#441)
- Tweaks to Event Series help text (#442)
- D8CORE-4668 Allow new google analytics account id format `G-` (#439)

8.x-2.10
--------------------------------------------------------------------------------
_Release Date: 2021-07-09_

- D8CORE-2594 Allow menu placement under unpublished pages (#435) (d1d4c4f)
- D8CORE-4497 Replaced <h3> with <p> in views no results text (0610b19)
- D8CORE-4235 Require alt text on gallery images (#433) (758231d)
- D8CORE-4504 Added mathjax for latex formula support (#434) (879cbb6)
- D8CORE-4194 Add url text for the link on media w caption paragraph (#423) (1122ead)
- Added and enabled the PDB module (#432) (de890ff)
- adding the skip to main content on these list page layouts that were missing (#431) (fc11f09)
- Corrected the events schedule display settings for the pattern refactor (aff23dc)
- Exported configs after Drupal 9.2.0 upgrade (#429) (a81ffde)

8.x-2.9
--------------------------------------------------------------------------------
_Release Date: 2021-06-11_

- Corrected acceptance tests with the latest dependency updates (#427) (30c2d25)
- D8CORE-4090 D8CORE-2888 accessbility changes to the news modules. (#425) (b0f2e0d)
- D8CORE-4339 Allow restriction based on the persons affiliation to the university (#424) (15ea056)
- D8CORE-2520 reset configs for events refactored patterns (#416) (54d8f44)
- D8CORE-3158 Enable more paragraph components on news and people (#420) (4bf9741)
- D8CORE-4245 Disable search module since search api exists (#421) (ceaf186)
- D8CORE-2898 Removed the headline field in favor of title field (#422) (0fe5ada)
- Updated stanford_ssp dev version to 2.x (d820722)

8.x-2.8
--------------------------------------------------------------------------------
_Release Date: 2021-05-07_

- adding the margin bottom to the intro for events. (#410) (e07a55a)
- Added User Search form (#412) (3167707)
- removing the margin top from the button on lists (#405) (d8d588e)
- fixing the pagination to the infinite scroll and a Load More button (#411) (751a62b)
- removed the extra h2 (#409) (a964622)
- Adjusted citation author fields to prevent only first name data entry (911a7ef)
- D8CORE-3104 D8CORE-3455 D8CORE-3456 D8CORE-3981 Help text and form tweaks (#408) (97b2d2e)
- D8CORE-2729 enable the hero banner behavior (#407) (1a9da8b)

8.x-2.7
--------------------------------------------------------------------------------
_Release Date: 2021-04-21_

- Changed the news card image back to a 2:1 ratio.

8.x-2.6
--------------------------------------------------------------------------------
_Release Date: 2021-04-19_

- Added experimental label to basic page types field and view
- D8CORE-4068 Allow publication authors to have only one name part (#402)

8.x-2.5
--------------------------------------------------------------------------------
_Release Date: 2021-04-19_

- Hotfix for tweaks to basic page+ functionality
- disabled "Preview" button on node form
- D8CORE-3480 Disallow image gallery media in the wysiwyg.

8.x-2.4
--------------------------------------------------------------------------------
_Release Date: 2021-04-12_

- Basic Page+ with views and teaser display (#388)
- Allow users to change the taxonomy term description text
- D8CORE-2572 Added and configured content_lock to prevent simultaneous edits (#395)
- Don't require the field on the global message form. (#357)
- updated view config to allow filtering past events lists (#397)
- D8CORE-3953 Replace "whitelist" with "allowed" for stanford_ssp (#394)
- fix double h2 tags (#396)
- D8CORE-2853 test for unpublishing the home page (#392)
- D8CORE-3538 Add "Other" citation type (#390)
- D8CORE-3458 Dont display unpublished profiles in the lists (#389)
- D8CORE-3945 Changed the news card image style for better resolution
- Corrected dependency in composer.json
- D8CORE-3536 Display publications as a teaser in the paragraph (#387)
- adding classes to the filtered by for publications in order to style them (#386)
- D8CORE-3126 Intranet Configs (#379)

8.x-2.3
--------------------------------------------------------------------------------
_Release Date: 2021-03-16_

- Added missing "See more publications button"

8.x-2.2
--------------------------------------------------------------------------------
_Release Date: 2021-03-11_

- Tweaked the publication term page heading language.
- Changed the order of the components on the publications edit page.


8.x-2.1
--------------------------------------------------------------------------------
_Release Date: 2021-03-05_

- Add "include child orgs" checkbox for cap org importer (#381) (9a5a388)
- D8CORE-3352-reroll: config changes for past events (#378) (8d3c6e0)
- Add and configure search api module to replace core search. (#371) (400b47c)
- fixing up merge conflicts. Removes the more publication … (#375) (0352bb5)
- Add a checkbox to the config pages for the drop down menu setting. (#374) (2e1e793)
- Revert "fixing spacing after the results for on the filtered page" (#377) (ebf4cba)
- Adjusted the text in the publication term view (0c91c68)
- fixing spacing after the results for on the filtered page (#376) (3bd01a8)
- Updated contrib dependencies (#373) (b91b945)
- fixup composer dependencies (4d0094c)
- D8CORE-3520 All Publications list page (#366) (4affad9)
- D8CORE-3476 Create a new view display mode specific for viewfields (#370) (abc52da)
- D8CORE-3463 Adjust the list and cards displays (#358) (e28b420)
- D8CORE-3050 Added path redirect import module (#362) (ecf58d9)
- D8CORE-3482 D8CORE-3484 Adjustments to publication lists and fields (#368) (48357dd)
- D8CORE-3137 Hide the published checkbox on taxonomy terms (#367) (e91b35f)
- D8CORE-3392 Changed the view blocks order and names (#364) (4c68f2c)
- D8CORE-1531 Removed "Remove formatting" ckeditor button that does nothing (#363) (eb8a1fd)
- Moved patches to shared package and use Stable 9 theme. (#359) (d4f05ac)
- changing spacing on more cards (#356) (a9dbbda)

8.x-2.0
--------------------------------------------------------------------------------
_Release Date: 2021-02-08_

- D8CORE-2968 Change the events schedule view (#354) (567973f)
- Several configuration tweaks. (#352) (11728b4)
- D8CORE-2585 Publications content type (#350) (326e730)
- added tests for dropdown menu (#349) (153da4e)
- Disable orphan actions on person importer (1bcd31c)
- D8CORE-3263 Create gallery paragraph type with colorbox actions (#347) (4815971)
- Exported configs after D9.1 database updates (#340) (9c84759)
- adding a class to the intro block (#345) (6187724)

8.x-1.22
--------------------------------------------------------------------------------
_Release Date: 2020-12-08_

- Hotfix: Don't escape people names with auto entity label.

8.x-1.21
--------------------------------------------------------------------------------
_Release Date: 2020-12-07_

- configs (#333) (2e35846)
- D8CORE-2867 Update image metadata tags (#339) (c755c6f)
- fixed separator between event dates (#338) (66d5989)
- D8CORE-000 Patch simplesamlphp auth module to prevent unwanted redirect (#337) (592cac5)
- D8CORE-3051 Allow paragraph views to display nested taxonomy content (#335) (0f4c59c)
- Updated default content module (#336) (b5de66f)
- D8CORE-2878 Add coalesce support for small animated gif fix (#334) (811649d)
- D8CORE-2759 Fix revisions table to show all revisions (#296) (ac8bd92)
- D8CORE-2957 Updated help section language (#330) (7efc961)
- D9 Ready (#329) (c69e05b)
- D8CORE-2495 Changed the link button in minimal html format (#332) (7c9a540)

8.x-1.20
--------------------------------------------------------------------------------
_Release Date: 2020-11-18_

- Updated test to work with drupal/core 8.9.9 (SA-CORE-2020-012)


8.x-1.19
--------------------------------------------------------------------------------
_Release Date: 2020-11-09_

- D8CORE-2957 Updated feedback form link url (#326) (748b713)
- D8CORE-2864 Allow site managers access to contextual links (#323) (1381642)
- D8CORE-2873 Added space for the event date (29cca26)
- D8CORE-2960 D8CORE-2961 Update paragraph and field names, with icons (#325) (d89ba4e)
- D8CORE-2570 Add intro block above event and news lists (#324) (c55f522)
- Add cache clear to prevent random test failure (f23c685)
- Updated default content for the paragraph row serializable data (#322) (d58e418)
- Switch card field classes (0597f53)
- D8CORE 000 entity card and view display tweaks (#321) (de83462)
- D8CORE-2780 Use square image style to make a circle (#320) (74667b7)
- Fixup for the composer.json (4b43e0e)
- D8CORE-2738 Add filters for event lists (#313) (2a88360)
- D8CORE-2720 D8CORE-2787 News card display for entity reference (#319) (467bf76)
- added flex-container to the people grid list view (a593c00)
- Patch token module (#317) (a1b2fd0)
- D8CORE-2885 People lists contextual filters and tests (#315) (4b1e77f)
- D8CORE 2710 News lists contextual filters and tests (#314) (76c6312)
- D8CORE-2875 Allow all paragraph types in tests (#312) (2a1d924)
- corrected composer.json (2780d18)
- D8CORE-2856 Add list and entity reference paragraph types (#310) (2b9f721)
- config adjustment to hide required asterisk for field group (#311) (f72a420)
- Adjusted the person importer to update the media item correctly (#309) (967b16d)
- Add process plugin to check image dimensions (#308) (6b9b7eb)

8.x-1.18
--------------------------------------------------------------------------------
_Release Date: 2020-10-05_

- enabled syslog as per recommendation from Acquia (5e8eb57)
- updated config after database updates (735e7c8)
- D8CORE-2685 Enable react behaviors module (#304) (4c2a1aa)
- Tests for manipulating taxonomy terms and the menu (#305) (0d08442)
- Tests for embeddable media form alters (#299) (a59d9b5)
- change the pagination counts (#297) (abd31cb)
- matching the person edit to the news edit (#301) (038a332)
- adding a Contact header to appear when there is any cont… (#302) (28d325f)
- Merge pull request #303 from SU-SWS/D8CORE-2538 (811e3a7)
- D8CORE-2538 Removed permissions for anonymous and a couple roles (c5edb6a)
- D8CORE-2538 Staff, Faculty, and Students shouldnt see the admin toolbar (5df751b)
- Merge pull request #300 from SU-SWS/D8CORE-2498_local-footer (f08ddfc)
- Merge pull request #281 from SU-SWS/D8CORE-2349-wrap-person (4918e77)
- Update composer.json (908e5d9)
- fixed branch name (59e0001)
- changed jumpstart_ui branch to pass tests (71c60d2)
- D8CORE-2498 Tests for local footer link paths (39d0d70)
- D8CORE-1200 Additional permission check for deleting home page (#298) (6ae14d5)
- Merge pull request #295 from SU-SWS/field-permission-unset (5f5b8d0)
- Merge branch '8.x-1.x' of https://github.com/SU-SWS/stanford_profile into D8CORE-2349-wrap-person (eab3a53)
- CAP-52: Add more fields to import list from cap. (8f3ffb1)
- removed module dependency (5bd68b0)
- Removed unnecessary third party settings (039dca4)
- removed field permissions junk (7970f81)
- added test for the fields (bcd8536)
- Update LocalFooterCest.php (1fe8ebb)
- dont create revisions (6439ae5)
- changed callback (32fffc9)
- CAP-52 Add 4 fields to be mapped from CAP data (46b7963)

8.x-1.17
--------------------------------------------------------------------------------
_Release Date: 2020-09-15_

- HOTFIX-000: Fix up unpublished content (#290) (fa3bd0d)

8.x-1.16
--------------------------------------------------------------------------------
_Release Date: 2020-09-14_

- Configs for embeddable media. (#273) (737163c)
- Settings for the maximum columns required (#284) (27d2037)
- Super Footer & Global Messages (#270) (0192522)
- Changed sort fields on people term pages (#282) (196675c)
- Updated configs for react paragraphs V2 (#275) (928ad75)
- Allow lockup option M (#278) (d243456)
- D8CORE-2499 Updated composer license (#274) (eb4b434)
- DEVGUIDE-000 Added html pre tag to allowed html tags. (#272) (798ef8b)
- Added extlink dependency (#261) (5e5579c)
- Update composer.json (#271) (9907620)

8.x-1.15
--------------------------------------------------------------------------------
_Release Date: 2020-08-07_

- fixed the codeception tests for the stack level
- Config Page for Lockup Configuration. (#220)
- D8CORE-2478 Updated html filter to allow colspan and rowspan. (#265)
- CSD-258: Reset selection when filtered. (#259)
- DEVOPS-000: Remove field formatter patch
- Added configs for responsive 1:1 image styles (#260)
- Removed views_taxonomy_term_name_depth not in use.
- Added self-escalation test.
- enabled diff module and clean up dependencies

8.x-1.14
--------------------------------------------------------------------------------
_Release Date: 2020-07-13_

- DEVOPS-000: Regenerated and cleaned up migration cron jobs
- Configure config ignore to allow for custom theme enabling
- DEVOPS-000: Allow the currently active theme settings to change
- DEVOPS-000: Added to config ignore to ignore the theme settings
- Changing the multirow gutters and removing the fixed widths
- DEVOPS-000: added subtheme test
- DEVOPS-000: use null value for update
- Added in the More news button
- DEVOPS-000: Merge pull request #239 from SU-SWS/D8CORE-2292
- DEVOPS-000: Merge pull request #236 from SU-SWS/cron-job-clean
- D8CORE-1538 Converting behat tests to codeception tests (#228)
- change telephone to contact
- Changing the multirow gutters and removing the fixed widths
- Configure config ignore to allow for custom theme enabling
- change telephone to contact.
- Added required fields and default image for profile. (#242)
- Remove everything code related from this profile into an
- D8CORE-2282 D8CORE-2293 Tests for admin toolbar links
- D8CORE-2282 D8CORE-2293 Fix the menu links access
- moved the cta into details area (#247)
- D8CORE-2178, D8CORE-2180, Adjust news node displays, field
- removing the action arrow on list items (#245)
- DEVOPS-000: Lock view unpublished to avoid the error in issue #3097251 (#250)
- DEVOPS-000: Update help text for Site URL (#252)
- removing the span as a wrapper (#249)
- DEVOPS-000: Merge pull request #246 from SU-SWS/D8CORE-2282
- D8CORE-2229 Update help section texts (#253)
- location changes to events (#251)

8.x-1.13
--------------------------------------------------------------------------------
_Release Date: 2020-06-17_

- Fix missing event title in views and re-ordered event node fields (#233)
- Removed a block on event display (#232)
- removed webform module since its not in use.
- Added notifications about events and person importer (#227)
- Removes labels from events views(#230)
- Use process plugin for the timezone field (#229)
- increased wait time for behat tests
- Adjusted Event Importer IDs. (#225)
- Require Drupal Core 8.9 & Contrib Updates (#224)
- Update to target IDs in default content and use the auto tagger. (#226)
- Event, Event Importer, Event Series (#212)
- Site managers can edit custom blocks (#223)
- screen reader only text for describing WYSIWYG paragraphs (#198)
- Stanford person importer. (#217)
- Additional Validation for canonical url field. (#218)
- Button stacking fix for horizontal alignment. (#216)
- Fixing the line height in the tables to em (#210)
- Removed 403 and 404 from xmlsitemap (#214)
- Changed label from body to description on card (#211)

8.x-1.12
--------------------------------------------------------------------------------
_Release Date: 2020-05-20_

- Set media with caption and wysiwyg to 3 in a row. (#208) (5878f09)
- Set cards to 3 in a row. (#206) (e6934b0)
- Changed headline on news to 180 max from 70. (#207) (a74ce2c)
- Remove news views paragraph type. (#205) (916fafb)

8.x-1.11
--------------------------------------------------------------------------------
_Release Date: 2020-05-18_

- Fixed the update hook to set the home, 404 and 403 pages.

8.x-1.10
--------------------------------------------------------------------------------
_Release Date: 2020-05-18_

- Added field permissions to restrict access to the site settings url field.
- Use drupal state & config overrides to set the home, 404, and 403 pages.

8.x-1.9
--------------------------------------------------------------------------------
_Release Date: 2020-05-15_

- Integrate stanford_news and stanford_person modules (#194) (ae4b392)
- Added stanford notifications module (#199) (014ed34)
- Getting buttons to stack (#174) (9dda5ce)
- fixing card image width for a single one on a no layout page (#197) (3fc7bda)
- D8CORE-1870 Change field labels on the card and banner paragraphs (#193) (0ad3673)
- removed bottom margin from last intro style (#190) (ffbb937)
- Fixed revision tab link test now that the name has changed (#192) (152d5e5)
- Tests for Fix links in the react widget (#189) (3f9f873)
- Fix misspelled eduPersonEntitlement (#188) (876ade6)
- D8CORE-1852 Setup node revision delete on stanford_page (#185) (ab7d7ac)
- Add media to linkit suggestions for inline links (#186) (4cec4e0)
- Use the stable version of layout_builder_modal module (#184) (614863e)

8.x-1.8
--------------------------------------------------------------------------------
_Release Date: 2020-04-17_

- Fixed Wrapping bug for the multi-row with 4 items.

8.x-1.7
--------------------------------------------------------------------------------
_Release Date: 2020-04-17_

- D8CORE-1847 fix link text getting encoded (#179)

8.x-1.6
--------------------------------------------------------------------------------
_Release Date: 2020-04-17_

- Make edit link clicking more specific (#157)
- update line lengths (#158)
- fixed seckit split (#161)
- D8CORE-1789 Fix xmlsitemap 404 response (#162)
- D8CORE-1708 Disable menu items in toolbar. (#165)
- Set up codeception tests framework (#153)
- Enabled a11y_checker for text editor. (#166)
- D8CORE-1664 Move to a release branch workflow with dev versions (#167)
- D8CORE-1816 wysiwyg tweaks (#175)
- D8CORE-1501 Added drush command to generate stress test node (#173)
- Dependency config update (#170)
- D8CORE-1197 Added google form media type (#169)
- D8CORE-1497 D8CORE-1681 Added spacer paragraph type and enabled multiple per row (#159)
- Multiple Per Row (#176)

8.x-1.5
--------------------------------------------------------------------------------
_Release Date: 2020-03-20_

- Added step in behat tests to wait for modal to be gone (#155)

8.x-1.4
--------------------------------------------------------------------------------
_Release Date: 2020-03-20_

- Bumped version of `views_bulk_operations` to 3.6 (#137)
- Allow site_manager to assign the site_manager role. (#138)
- Changed default workgroup for saml role mapping to uit:sws so Marco doesn't get admin. (#140)
- Added alt text to default content. (#141)
- Created a config split for acsf modules. (#131)
- Check for node instance. (#145)
- Add Paranoia module. (#143)
- Updated behat test to use existing step definition "Then I click" (fixes bug in tests)
- wysiwyg tweaks. (#144)
- Added more image styles for wysiwyg. (#146)
- Allow custom blocks in layout builder. (#149)
- Set image metatags to use an image style. (#148)
- Add administer media permission to site managers. (#150)
- Changed stanford_page_layout_full layout option to only be on stanford pages. (#151)


8.x-1.3
--------------------------------------------------------------------------------
_Release Date: 2020-02-27_

- Full width page only stylesheet. (#118)
- Adds validation to config form uri. (#112)
- Invalidate node page cache when new menu items are added as children. (#119)
- D8CORE-1340 Use core link in wysiwyg for now (#120)
- Do not limit url characters in local footer (#121)
- Alter menu form and status messages for config_readonly users (#114)
- Change filter-format to allow for html entities. (#123)
- Fixed drupal/paragraphs version (#117)
- Set home link to the home node for default content (#124)
- Enable new filter in the full html format. (#125)
- Fix for subtitle/subhead styles (#110)
- Disable nobots via state on site install (#128)
- Kill margin-bottom on <p> tag (#130)

8.x-1.2
--------------------------------------------------------------------------------
_Release Date: 2020-02-21_

- Resynced the media library view with the drupaDl core version (#105)
- D8CORE-1393 Dont display page title block on 404 and 403 pages (#106)
- Add Media with Caption paragraph. (#59)
- Dont show contextual links to users. (#108)
- Change "Related Text" to "Card Text". (#108)
- Moved banner to top banner field. (#108)
- Allow <br> tags. (#108)
- Add help text to layout selection field. (#108)
- Allow sitemanagers to view all unpublished content. (#108)
- Moving the ol and ul styles to the wysiwyg (#111)
- Added stanford media library to node form since react doesnt add it (#113)


8.x-1.1
--------------------------------------------------------------------------------
_Release Date: 2020-02-14_

- Happy Valentines Day!
- WYSIWYG heading text alignment (#87)
- Add missing breakpoint for 2:1 card responsive image style
- Adjust content/nav gap. (#89)
- Added page title for non-node pages (#92)
- Text alignment for lists (#95)
- Added field_formatter_class module (#93)
- Update homepage banner default content (#96)
- Enable metatags and XMLSitemap on prod (#88)
- Alter Node Edit Form for react_paragraphs (#97)
- D8CORE-1201 use confirm_leave module (#99)
- D8CORE-1007 Add image for default content (#100)
- Added github template files.
- Uninstall better_normalizers module
- Uninstall config_distro module
- Updated default content and media library images.
- Add image for default content (#100)
- Modified the full width layout (#102)

8.x-1.0
--------------------------------------------------------------------------------
_Release Date: 2020-02-05_

- Stable release!
- Many configuration updates
- Many style updates
- Many new tests, both unit and behat.
- Menu tweaks for primary and seconday navigation
- Removed H6 from stanford_text formats
- Page Authoring: style banner top
- fix home and no 2nd menu
- WYSIWYG Typography update and CKEditor styles
- Provide additional help information
- Add anchor location for skip links to content layouts
- Switch ot React Paragraphs
- Override WYSIWYG text paragraph styles
- Dark paragraph type icons
- Adjust content and media menu item titles
- SAML role mapping config page
- line length fix
- Removed stanford_profile_install from being installed as it has nothing to do.

8.x-1.0-alpha5
--------------------------------------------------------------------------------
_Release Date: 2020-01-23_

- Prevent home page from being deleted (#68)
- Allow classes on header elements (#67)
- Fix for focal point config mis-match and failed circle ci behat tests.

8.x-1.0-alpha4
--------------------------------------------------------------------------------
_Release Date: 2020-01-22_

- adding wysiwyg scss file (#60)
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
