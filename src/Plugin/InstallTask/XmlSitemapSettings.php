<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Drupal\stanford_profile\InstallTaskBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Install users task.
 *
 * @InstallTask(
 *   id="stanford_profile_xml_sitemap"
 * )
 */
class XmlSitemapSettings extends InstallTaskBase implements ContainerFactoryPluginInterface {

  /**
   * State Service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('state')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->state = $state;
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(array &$install_state) {
    if (!static::isAhEnv()) {
      return;
    }

    $site_name = $install_state['forms']['install_configure_form']['site_name'] ?? FALSE;
    if (!$site_name) {
      return;
    }
    $this->state->set('xmlsitemap_base_url', "https://$site_name.sites.stanford.edu");

    $rebuild_types = xmlsitemap_get_rebuildable_link_types();
    if (empty($rebuild_types)) {
      return;
    }

    // Rebuild the xml sitemap.
    $batch = xmlsitemap_rebuild_batch($rebuild_types, TRUE);
    batch_set($batch);
    drush_backend_batch_process();
  }

}
