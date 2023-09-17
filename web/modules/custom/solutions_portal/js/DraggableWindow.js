(function ($, Drupal) {

  'use strict';

  let taxonomyTree = $('div[id*="_taxonomy_tree"]');
  $('#edit-field-industries-drag-window').jqxWindow({
    autoOpen: false,
    width: 450,
    height: 450,
  });
  $('#field_industries_taxonomy_tree').jqxTree({
    theme: 'summer',
    height: '300px',
    width: '300px',
  });

  $('#edit-field-industries-button').on('click', () => {
    $('#edit-field-industries-drag-window').jqxWindow('open');
  })

  $('#field_industries_taxonomy_tree').click(function () {
    let item = $('#field_industries_taxonomy_tree').jqxTree('getSelectedItem');
    if(item.hasItems) {
      $('#field_industries_taxonomy_tree').jqxTree('selectItem', null);
      return;
    }
  });

  $("#edit-field-industries-drag-window-search").on('keyup', () => {
    let items = $('#field_industries_taxonomy_tree').jqxTree("getItems");
    let searchedValue = $("#edit-field-industries-drag-window-search").val();
    for (let i = 0; i < items.length; i++) {
      if (searchedValue == items[i].label) {
        $('#field_industries_taxonomy_tree').jqxTree('expandItem', items[i].parentElement);
        $('#field_industries_taxonomy_tree').jqxTree('selectItem', items[i]);
      }
    }
  });

  $('.select-term').on('click', () => {
    let item = $('#field_industries_taxonomy_tree').jqxTree('getSelectedItem');
    if(item != null) {
      $('#edit-field-industries-target-id').val(item.label);
    }
  })



  $('.open-drag-window').on('click', function() {


  });

})(jQuery, Drupal);
