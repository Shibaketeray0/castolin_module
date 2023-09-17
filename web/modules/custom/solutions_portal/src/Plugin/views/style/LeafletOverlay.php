<?php

namespace Drupal\solutions_portal\Plugin\views\style;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\Leaflet\LeafletService;
use Drupal\leaflet_views\Plugin\views\style\LeafletMap;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Style plugin to render a View output as a Leaflet map with overlay image.
 *
 * @ingroup views_style_plugins
 *
 * Attributes set below end up in the $this->definition[] array.
 *
 * @ViewsStyle(
 *   id = "solutions_portal",
 *   title = @Translation("Leaflet Overlay"),
 *   help = @Translation("Displays a View as a Leaflet map with overlay
 *   image."),
 *   display_types = {"normal"},
 *   theme = "leaflet-map"
 * )
 */
class LeafletOverlay extends LeafletMap {

  /**
   * Path type manager.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected CurrentPathStack $pathManager;

  /**
   * Constructs a LeafletMap style instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display
   *   The entity display manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Leaflet\LeafletService $leaflet_service
   *   The Leaflet service.
   * @param \Drupal\Core\Utility\LinkGeneratorInterface $link_generator
   *   The Link Generator service.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_manager
   *   The field type plugin manager service.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The path manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityDisplayRepositoryInterface $entity_display,
    AccountInterface $current_user,
    MessengerInterface $messenger,
    RendererInterface $renderer,
    ModuleHandlerInterface $module_handler,
    LeafletService $leaflet_service,
    LinkGeneratorInterface $link_generator,
    FieldTypePluginManagerInterface $field_type_manager,
    CurrentPathStack $current_path,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_manager, $entity_field_manager, $entity_display, $current_user, $messenger, $renderer, $module_handler, $leaflet_service, $link_generator, $field_type_manager);
    $this->pathManager = $current_path;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('entity_display.repository'),
      $container->get('current_user'),
      $container->get('messenger'),
      $container->get('renderer'),
      $container->get('module_handler'),
      $container->get('leaflet.service'),
      $container->get('link_generator'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('path.current')
     );
  }

  /**
   * Returns an image path from the context node.
   *
   * @return string
   *   Image path.
   */
  public function getImgPath() : string {
    $route_match = $this->pathManager->getPath();
    $elems = explode('/', $route_match);
    $node_id = $elems[2];
    $node = $this->entityManager
      ->getStorage('node')
      ->load($node_id);

    if (
      $node->hasField('field_background_image')
      && !$node->get('field_background_image')->isEmpty()
    ) {
      $image_name = $node->get('field_background_image')->entity->getFileUri();
      return substr($image_name, 8);
    }
    else {
      return '';
    }

  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $element = parent::render();
    $element['#attached']['library'][] = 'solutions_portal/leaflet_view';
    foreach ($element['#attached']['drupalSettings']['leaflet'] as &$map) {
      unset($map['map']['layers']);
      $url = '/sites/default/files' . $this->getImgPath();
      $map['map']['overlay'] = [
        'imageUrl' => $url,
      ];
    }
    return $element;
  }

  /**
   * Set default options.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['image_overlay'] = ['default' => ''];
    return $options;
  }

}
