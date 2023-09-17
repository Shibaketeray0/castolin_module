<?php

namespace Drupal\solutions_portal\Plugin\FormAlter;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\pluginformalter\Plugin\FormAlterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements a form alter class for modifying 'Synoptec' taxonomy creation.
 *
 * @FormAlter(
 *   id = "solutions_portal_synoptec_form_alter",
 *   label = @Translation("Altering synoptec taxonomy creation form."),
 *   form_id = {
 *    "taxonomy_term_synoptec_form"
 *   },
 * )
 *
 * @package Drupal\solutions_portal\Plugin\FormAlter
 */
class SolutionsPortalSynoptecFormAlter extends FormAlterBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new SolutionsPortalBaseFormAlter object.
   *
   * @param array $configuration
   *   Configuration settings or options for the form alter plugin.
   * @param string $plugin_id
   *   Unique identifier for the form alter plugin.
   * @param array $plugin_definition
   *   Definition and metadata for the form alter plugin.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
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
  public function formAlter(array &$form, FormStateInterface &$form_state, $form_id) {
    $form['#attached']['library'][] = 'solutions_portal/synoptec_leaflet';

    $form['field_synoptec']['widget']['#ajax'] = [
      'callback' => [$this, 'changeLeafletCallback'],
      'event' => 'change',
      'disable-refocus' => TRUE,
    ];

    $form['#attached']['drupalSettings']['solutions_portal']['image_path'] = '';

    $synoptec_category_id = NestedArray::getValue($form['field_synoptec'], [
      'widget', '#default_value', 0,
    ]);

    if ($synoptec_category_id) {
      $node = $this->entityTypeManager
        ->getStorage('node')
        ->load($synoptec_category_id);

      $image_path = substr($node->get('field_background_image')->entity->getFileUri(), 8);
      $form['#attached']['drupalSettings']['solutions_portal']['image_path'] = $image_path;
    }

  }

  /**
   * Sends an image path to js.
   *
   * @param array &$form
   *   A reference to the form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An AjaxResponse object containing commands to send an image path to js.
   */
  public function changeLeafletCallback(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();

    if ($form_state->getValue('field_synoptec') != NULL) {
      $selected_value = $form_state->getValue('field_synoptec');
      $node = $this->entityTypeManager->getStorage('node')->load($selected_value[0]['target_id']);
      $response->addCommand(new InvokeCommand(NULL, 'changeLeafletCallback', [substr($node->get('field_background_image')->entity->getFileUri(), 8)]));
    }

    return $response;
  }

}
