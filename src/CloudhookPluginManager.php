<?php

namespace Drupal\cloudhooks;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\CategorizingPluginManagerTrait;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Cloudhook plugin manager.
 */
class CloudhookPluginManager extends DefaultPluginManager implements CloudhookPluginManagerInterface {

  use CategorizingPluginManagerTrait;

  /**
   * Constructs CloudhookPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/Cloudhook',
      $namespaces,
      $module_handler,
      'Drupal\cloudhooks\CloudhookPluginInterface',
      'Drupal\cloudhooks\Annotation\Cloudhook'
    );
    $this->alterInfo('cloudhook_info');
    $this->setCacheBackend($cache_backend, 'cloudhook_plugins');
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackPluginId($plugin_id, array $configuration = []) {
    return 'cloudhook';
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitionsForEvent($event = NULL) {
    $definitions = array_filter($this->getDefinitions(), function ($definition) use ($event) {
      return !$event || in_array($event, $definition['events']);
    });
    uasort($definitions, function ($a, $b) {
      return $a['weight'] - $b['weight'];
    });
    return $definitions;
  }

}
