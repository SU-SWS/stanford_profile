# Stanford Intranet

## Key Points
Here are some key points of the intranet functionality.
* Entire site protected. There is no mixed access content. Meaning every page within the site will require an authenticated user.
* Role specified access
  * Site managers will be able to assign access to pages based on a desired role
  * Roles can be created and mapped via simple saml authentication.
  * Custom roles have no permissions and are only intended for intranet designation.
* Media content does not have role specified access.
  * Due to the complexities with media prevents media access inheritance.
  * If a user can log into the site, they have access to all media within the site
* SAML restrictions
  * Site managers will be able to restrict roles and sunets login access. This will prevent unwanted users from having access to the intranet.
* The module uses Drupal's [State API](https://www.drupal.org/docs/8/api/state-api/overview) to enable the Intranet:
  * State is not meant to be exported.
  * State is specific to an individual environment.
  * State is not to be deployed to another environment.
  * All state information is lost when you reset the database.

## Enable Intranet
To enable the intranet functionality use the command:
1. `drush @<site_alias> sset stanford_intranet 1`
1. `drush @<site_alias> cr`

It is possible to disable the intranet later, but it is not recommended due to the media access and potential exposure of sensitive information.

## Allow File Uploads
**IMPORTANT:** Media content does not have role specified access and allowing file uploads can open up security risks. All files are accessible by anyone with access to the site (any authenticated user). Allowing file uploads should only be enabled in specific and highly-vetted circumstances.

1. `drush sset stanford_intranet.allow_file_uploads 1`
1. `drush @<site_alias> cr`

## Additional Resources
* [SWS Dev Guide: Intranet](https://sws-devguide.stanford.edu/site-building/drupal-8/intranet)
* [Drupal State API Overview](https://www.drupal.org/docs/8/api/state-api/overview)