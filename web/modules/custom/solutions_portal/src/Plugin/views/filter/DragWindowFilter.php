<?php
//
//namespace Drupal\solutions_portal\Plugin\views\filter;
//
//use Drupal\Core\Form\FormStateInterface;
//use Drupal\taxonomy\Entity\Vocabulary;
//use Drupal\views\Plugin\views\filter\FilterPluginBase;
//
///**
// * Filter by selected taxonomy terms.
// *
// * @ingroup views_filter_handlers
// *
// * @ViewsFilter("drag_window")
// */
//class DragWindowFilter extends FilterPluginBase {
//
//  /**
//   * {@inheritdoc}
//   */
//  public function defineOptions() {
//    $options = parent::defineOptions();
//
//    $options['vocabulary'] = ['default' => NULL];
//
//    return $options;
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function getValueOptions() {
//    // Get all vocabularies.
//    $vocabularies = Vocabulary::loadMultiple();
//
//    $options = [];
//    foreach ($vocabularies as $vocabulary) {
//      $options[$vocabulary->id()] = $vocabulary->label();
//    }
//
//    return $options;
//  }
//
//  /**
//   *
//   */
////  public function buildTree($term) {
////
////    $has_children = \Drupal::entityTypeManager()
////      ->getStorage('taxonomy_term')
////      ->loadChildren($term->tid);
////
////    if (!empty($has_children)) {
////      $options_terms[$term->tid] = '<li>' . $term->name . '</li>' . '<ul>';
////      foreach ($has_children as $child) {
////        $options_terms[$term->tid].='<li>' . $child->name . '</li>';
////      }
////      $options_terms[$term->tid].='</ul>';
////    }
////    else {
////      $options_terms[$term->tid] = '<li>' . $term->name . '</li>';
////    }
////    return $options_terms[$term->tid];
////
////  }
//
//  /**
//   *
//   */
//  private function getTermOptions($vocabulary_id) {
//    $options_terms = [];
//
//    if (!empty($vocabulary_id)) {
//      $tree = \Drupal::entityTypeManager()
//        ->getStorage('taxonomy_term')
//        ->loadTree($vocabulary_id);
//
//      foreach ($tree as $term) {
//        // Indent terms based on their depth in the hierarchy.
//        $indent = str_repeat('-', $term->depth);
//        $term_id = $term->tid;
//        $term_name = $term->name;
//        $options_terms[$term_id] = $indent . $term_name;
//      }
//    }
//
//    return $options_terms;
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
//    parent::buildOptionsForm($form, $form_state);
//
//    $form['vocabulary'] = [
//      '#type' => 'select',
//      '#title' => $this->t('Select Vocabulary'),
//      '#options' => $this->getValueOptions(),
//      '#default_value' => $this->options['vocabulary'],
//      '#required' => TRUE,
//    ];
//
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function buildExposedForm(&$form, FormStateInterface $form_state) {
//
//    $selected_value = $this->options['vocabulary'];
//
//    $form['search'] = [
//      '#type' => 'textfield',
//      '#title' => 'search',
//      '#prefix' => '<div id="drag"><div>Header</div><div>',
//    ];
//
//    $form['term'] = [
//      '#type' => 'select',
//      '#title' => t('Select Term'),
//      '#options' => $this->getTermOptions($selected_value),
//      '#multiple' => TRUE,
//      ];
//
//    $form['submit'] = array (
//      '#type' => 'submit',
//      '#buttontype' => 'button',
//      '#value' => 'Select',
//      '#suffix' => '</div></div>',
//
//    );
//
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function query() {
//    $this->ensureMyTable();
//    $this->query->addWhere($this->options['group'], "$this->tableAlias.'field_basemetal_target_id'", $this->value, $this->operator);
//  }
//
//}
