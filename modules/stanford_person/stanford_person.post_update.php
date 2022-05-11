<?php

/**
 * @file
 * File description.
 *
 * Long description.
 */

/**
 * Install the default profile image.
 *
 * Add the image to the media library and add the entities to the DB.
 */
function stanford_person_post_update_8100() {
  _stanford_person_install_default_photo();
}
