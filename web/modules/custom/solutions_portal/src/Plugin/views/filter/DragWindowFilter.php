<?php

namespace Drupal\solutions_portal\Plugin\views\filter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter by selected taxonomy terms.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("drag_window")
 */
class DragWindowFilter extends FilterPluginBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defineOptions() : array {
    $options = parent::defineOptions();

    $options['vocabulary'] = ['default' => NULL];

    return $options;
  }

  /**
   * Get all taxonomy vocabularies.
   */
  public function getValueOptions() : array {
    // Get all vocabularies.
    $vocabularies = Vocabulary::loadMultiple();

    $options = [];
    foreach ($vocabularies as $vocabulary) {
      $options[$vocabulary->id()] = $vocabulary->label();
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['expose_button']['checkbox']['checkbox']['#required'] = TRUE;
    $form['vocabulary'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Vocabulary'),
      '#options' => $this->getValueOptions(),
      '#default_value' => $this->options['vocabulary'],
      '#required' => TRUE,
    ];
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
  public function buildExposedForm(&$form, FormStateInterface $form_state) {
    $selected_value = $this->options['vocabulary'];

    $tree[] = $this->load($selected_value);

    $form['#attached']['drupalSettings']['solutions_portal'][$selected_value] = $tree;


    $form[$selected_value . '_button'] = [
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


    $form[$selected_value . '_drag_window'] = [
      '#title' => 'Please select an item',
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['drag-window'],
      ],
    ];


    $form[$selected_value . '_drag_window'][$selected_value . '_tree'] = [
      '#theme' => 'entity-tree',
      '#field_id' => $selected_value,
    ];

    $form[$selected_value . '_drag_window'][$selected_value . '_select_button'] = [
      '#type' => 'button',
      '#value' => 'Select',
      '#executes_submit_callback' => FALSE,
      '#limit_validation_errors' => [],
      '#attributes' => [
        'onclick' => 'return false;',
        'class' => ['select-term-to-query'],
      ],
      '#attached' => [
        'library' => [
          'solutions_portal/draggable_window',
        ]
      ]
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
//    $this->query->addWhere($this->options['term'], "$this->tableAlias.'field_basemetal_target_id'", $this->value, $this->operator);
  }

}
