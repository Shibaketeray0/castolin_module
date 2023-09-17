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
 * Implements a form alter class for modifying 'Application' creation and edit.
 *
 * @FormAlter(
 *   id = "solutions_portal_form_alter",
 *   label = @Translation("Altering application creation and edit forms."),
 *   form_id = {
 *    "node_application_form",
 *    "node_application_edit_form"
 *   },
 * )
 *
 * @package Drupal\solutions_portal\Plugin\FormAlter
 */
class SolutionsPortalBaseFormAlter extends FormAlterBase implements ContainerFactoryPluginInterface {

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
    $form['#attached']['library'][] = 'solutions_portal/application_formulas';

    $required_fields = [
      'field_advantages',
      'field_customer_name',
      'field_customer_address',
      'field_description',
      'field_initiator',
      'field_eco_cs_newpartunitprice',
      'field_eco_fs_newpartunitprice',
      'field_eco_cs_nrassemblybypart',
      'field_eco_fs_nrassemblybypart',
      'field_eco_cs_nrofmonthperyear',
      'field_eco_fs_nrofmonthperyear',
      'field_eco_numberofparts',
      'field_eco_cs_possibletreatments',
      'field_eco_fs_possibletreatments',
      'field_eco_cs_partlife',
      'field_eco_fs_partlife',
      'field_previous_solution',
      'field_processes',
      'field_solution',
      'field_eco_cs_unitcostassembly',
      'field_eco_fs_unitcostassembly',
      'field_eco_cs_unitcostoftreatment',
      'field_eco_fs_unitcostoftreatment',
      'field_weight',
    ];

    $form_display = $form_state->getStorage()['form_display'];

    if ($form_display->getMode() === 'far_terolink_convertation') {
      foreach ($required_fields as $required_field) {
        $form[$required_field]['widget'][0]['value']['#required'] = TRUE;
      }
    }

    $form['field_basemetal']['widget'][0]['target_id']['#ajax'] = [
      'callback' => [$this, 'baseMetalCallback'],
      'event' => 'change',
      'disable-refocus' => TRUE,
    ];

    $form['field_country']['widget']['#ajax'] = [
      'callback' => [$this, 'costPricesCallback'],
      'event' => 'change',
      'disable-refocus' => TRUE,
    ];

    $prices_fields = [
      'field_annual_customer_savings',
      'field_eco_cs_annualoperatingcost',
      'field_eco_fs_annualoperatingcost',
      'field_cost_of_castolin',
      'field_eco_cost_reduction',
      'field_eco_cs_maintenancecost',
      'field_eco_fs_maintenancecost',
      'field_eco_cs_costassembly',
      'field_eco_fs_costassembly',
      'field_downtime_cost_per_day',
      'field_eco_cs_newpartscost',
      'field_eco_fs_newpartscost',
      'field_eco_cs_newpartunitprice',
      'field_eco_fs_newpartunitprice',
      'field_price_per_new_part',
      'field_eco_cs_totallifecost',
      'field_eco_fs_totallifecost',
      'field_eco_cs_unitcostassembly',
      'field_eco_fs_unitcostassembly',
      'field_eco_cs_unitcostoftreatment',
      'field_eco_fs_unitcostoftreatment',
    ];

    if (NestedArray::getValue($form['field_country'],
            ['widget', '#default_value', 0]) != NULL) {
      $country_id = $form['field_country']['widget']['#default_value'][0];
      $node = $this->entityTypeManager->getStorage('node')->load($country_id);
      $allowed_currencies = $node->get('field_currency')->getSettings()['allowed_values'];
      $property = $node->get('field_currency')->getValue();

      foreach ($prices_fields as $price_field) {
        $actual_field_title = $form[$price_field]['widget'][0]['value']['#title'];
        $form[$price_field]['widget'][0]['value']['#title'] = substr($actual_field_title, 0, -1) . " " . $allowed_currencies[$property[0]['value']] . ")";
      }
    }

    if (NestedArray::getValue($form['field_basemetal'],
                ['widget', 0, 'target_id', '#default_value']) != NULL && $form['field_is_terolink']['widget']['#default_value'][0] == NULL) {
      $basemetal = $form['field_basemetal']['widget'][0]['target_id']['#default_value'];
      $basemetal_property_value = $basemetal->field_property->value;
      $form['field_co2reductionfactor']['widget'][0]['value']['#value'] = $basemetal_property_value;
    }

    $fields_to_use_for_weight = [
      'field_eco_fs_unitcostoftreatment',
      'field_eco_fs_unitcostassembly',
      'field_eco_cs_newpartunitprice',
      'field_eco_cs_unitcostoftreatment',
      'field_eco_cs_unitcostassembly',
    ];

    $base_field_id = 'field_eco_numberofparts_';
    $state = [
      'visible' => [
        ':input[name="field_is_terolink"]' => ['value' => '2'],
      ],
    ];

    for ($i = 1; $i <= count($fields_to_use_for_weight); $i++) {
      $current_field_id = $base_field_id . $i;

      $form[$current_field_id] = $form['field_eco_numberofparts'];
      $form[$current_field_id]['#disabled'] = TRUE;
      $form[$current_field_id]['#states'] = $state;
      $form[$current_field_id]['#weight'] = $form[$fields_to_use_for_weight[$i - 1]]['#weight'] - 1;
    }
  }

  /**
   * Updates 'CO2 Calculation Factor' based on selected base metal property.
   *
   * @param array &$form
   *   A reference to the form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An AjaxResponse object containing commands to update the form element.
   */
  public function baseMetalCallback(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $application_type = $form_state->getValue('field_is_terolink');

    if ($application_type[0]['value'] == '2' || $application_type[0]['value'] == NULL) {
      $selected_value = $form_state->getValue('field_basemetal');
      $term = $this->entityTypeManager->getStorage('taxonomy_term')
        ->load($selected_value[0]['target_id']);
      $property_value = $term->get('field_property')->getValue();
      $response->addCommand(new InvokeCommand('#edit-field-co2reductionfactor-0-value', 'val', [$property_value[0]['value']]));
    }

    return $response;
  }

  /**
   * Updates currency in titles based on 'Currency' of selected country.
   *
   * @param array &$form
   *   A reference to the form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An AjaxResponse object containing commands to update the form element.
   */
  public function costPricesCallback(array &$form, FormStateInterface $form_state): AjaxResponse {
    $prices_fields_id = [
      'edit-field-downtime-cost-per-day-0-value',
      'edit-field-price-per-new-part-0-value',
      'edit-field-cost-of-castolin-0-value',
      'edit-field-annual-customer-savings-0-value',
      'edit-field-eco-fs-newpartunitprice-0-value',
      'edit-field-eco-fs-newpartscost-0-value',
      'edit-field-eco-fs-unitcostoftreatment-0-value',
      'edit-field-eco-fs-maintenancecost-0-value',
      'edit-field-eco-fs-unitcostassembly-0-value',
      'edit-field-eco-fs-costassembly-0-value',
      'edit-field-eco-fs-totallifecost-0-value',
      'edit-field-eco-fs-annualoperatingcost-0-value',
      'edit-field-eco-cs-newpartunitprice-0-value',
      'edit-field-eco-cs-newpartscost-0-value',
      'edit-field-eco-cs-unitcostoftreatment-0-value',
      'edit-field-eco-cs-maintenancecost-0-value',
      'edit-field-eco-cs-unitcostassembly-0-value',
      'edit-field-eco-cs-costassembly-0-value',
      'edit-field-eco-cs-totallifecost-0-value',
      'edit-field-eco-cs-annualoperatingcost-0-value',
      'edit-field-eco-cost-reduction-0-value',
    ];

    /**
     * Function converts field(input) ID into field name.
     */
    function getFieldName($field_id): string {
      $field_title = str_replace(['edit-', '-0-value'], '', $field_id);
      return str_replace('-', '_', $field_title);
    }

    $response = new AjaxResponse();

    if ($form_state->getValue('field_country') != NULL) {
      $selected_value = $form_state->getValue('field_country');
      $node = $this->entityTypeManager->getStorage('node')->load($selected_value[0]['target_id']);
      $allowed_currencies = $node->get('field_currency')->getSettings()['allowed_values'];
      $property = $node->get('field_currency')->getValue();

      foreach ($prices_fields_id as $field_id) {
        // Replace currency by pattern, in case it's already displayed.
        $actual_field_title = preg_replace('/[A-Z]{3}/', '', $form[getFieldName($field_id)]['widget'][0]['value']['#title']);
        $form[getFieldName($field_id)]['widget'][0]['value']['#title'] = '';
        $response->addCommand(new InvokeCommand("label[for='" . $field_id . "']", 'text', [substr($actual_field_title, 0, -1) . " " . $allowed_currencies[$property[0]['value']] . ")"]));
      }
    }
    else {
      foreach ($prices_fields_id as $field_id) {
        $actual_field_title = $form[getFieldName($field_id)]['widget'][0]['value']['#title'];
        $response->addCommand(new InvokeCommand("label[for='" . $field_id . "']", 'text', [$actual_field_title]));
      }
    }
    return $response;
  }

}
