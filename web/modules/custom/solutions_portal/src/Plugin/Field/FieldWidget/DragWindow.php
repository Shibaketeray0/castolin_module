<?php

namespace Drupal\solutions_portal\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tree entity reference widget in draggable window.
 *
 * @FieldWidget(
 *   id = "tree_drag_window",
 *   label = @Translation("Tree entity reference widget"),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   multiple_values = TRUE
 * )
 */
class DragWindow extends EntityReferenceAutocompleteWidget
{

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager)
  {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  public function getBundles($taxonomy_bundles)
  {
    $bundles = [];
    foreach ($taxonomy_bundles as $key => $bundle) {
      $this->bundles[] = $bundle;
    }
    return $bundles;
  }

  /**
   * Load taxonomy terms tree from vocabulary.
   *
   * @param $vocabulary
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function load($vocabulary) {
    return $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vocabulary);
  }


  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $arr_element = parent::formElement($items, $delta, $element, $form, $form_state);

    $field_definition = $items->getFieldDefinition();

    $title = $field_definition->getLabel();

    $field_name = $field_definition->getName();


    $taxonomy_bundles = $field_definition->getSetting('handler_settings')['target_bundles'];

    $tree = [];
    foreach ($taxonomy_bundles as $key => $bundle) {
      $tree[] = $this->load($bundle);
    }
    $form['#attached']['drupalSettings']['solutions_portal'][$field_name] = $tree;

    $arr_element['target_id'] += [
      '#title' => $title,
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#selection_settings' => [
        'target_bundles' => $this->getBundles($taxonomy_bundles),
      ]
    ];

    $arr_element['button'] = [
      '#type' => 'button',
      '#value' => 'Open',
      '#executes_submit_callback' => FALSE,
      '#limit_validation_errors' => [],
      '#attributes' => [
        'onclick' => 'return false;',
        'class' => ['open-drag-window']
      ],
      '#attached' => [
        'library' => [
          'solutions_portal/draggable_window',
        ]
      ]
    ];

    $arr_element['drag_window'] = [
      '#title' => 'Please select an item',
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['drag-window'],
      ],
    ];


    $arr_element['drag_window']['tree'] = [
      '#theme' => 'entity-tree',
      '#terms' => $tree,
      '#field_id' => $field_name,
    ];

    $arr_element['drag_window']['select_button'] = [
      '#type' => 'button',
      '#value' => 'Select',
      '#executes_submit_callback' => FALSE,
      '#limit_validation_errors' => [],
      '#attributes' => [
        'onclick' => 'return false;',
        'class' => ['select-term'],
      ],
      '#attached' => [
        'library' => [
          'solutions_portal/draggable_window',
        ]
      ]
    ];

    return $arr_element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state)
  {
    return $values['target_id'];
  }
}




