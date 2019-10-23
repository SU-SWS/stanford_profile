<?php

namespace Drupal\stanford_profile;

/**
 * Interface InstallTasksInterface
 *
 * @package Drupal\stanford_profile
 */
interface InstallTasksInterface {

  const DEFAULT_SITE = 'default';

  /**
   * Service now api endpoint.
   */
  const SNOW_API = 'https://stanford.service-now.com/api/stu/su_acsf_site_requester_information/requestor';

  /**
   * Call the SNOW API and set settings and add users.
   *
   * @param string $site_name
   *   The requested site name.
   */
  public function setSiteSettings($site_name);

}
