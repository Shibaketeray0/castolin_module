(function ($, Drupal, drupalSettings ) {

  'use strict';

  let tree = $('.taxonomy-tree');

  let openButton = $('.open-drag-window');


  $('.drag-window').jqxWindow({
    autoOpen: false,
    width: 450,
    height: 450,
  });


  openButton.on('click', (e) => {
    let buttonId = e.currentTarget.id;

    let mutualId = buttonId.substring(0, buttonId.length - 7);

    let treeId = mutualId.substring(5, mutualId.length);
    treeId = treeId.replaceAll("-", "_");
    $(`fieldset[id*="${mutualId}"`).jqxWindow('open');

    let taxonomy_tree = drupalSettings.solutions_portal[treeId];

    let data = taxonomy_tree[0].map(element => ({
      tid: element.tid,
      name: element.name,
      parent: element.parents.length > 0 ? element.parents[0] : null,
    }));

    let source = {
      datatype: "json",
      dataFields: [
        { name: "tid", type: "string" },
        { name: "name", type: "string" },
        { name: "parent", type: "string" }
      ],
      hierarchy:
        {
          keyDataField: { name: 'tid' },
          parentDataField: { name: 'parent'  }
        },
      id: 'tid',
      localData: data,
      selectionMode: 'singleRow'
    };

    let dataAdapter = new $.jqx.dataAdapter(source);

    tree.jqxTreeGrid({
      source: dataAdapter,
      columns: [
        { text: "Name", dataField: "name" },
      ],
      filterable: true,
    });

  });

  $('.select-term').on('click', (e) => {
    let buttonId = e.currentTarget.id;

    let mutualId = buttonId.substring(5, buttonId.length - 26);

    let underScoresId = mutualId.replaceAll("-", "_");

    let treeId = '#' + underScoresId + '_taxonomy_tree';

    let tree = $(treeId);

    let selectedItems = tree.jqxTreeGrid('getSelection');

    let selectedItemsNames = [];
    selectedItems.map(item => {
      selectedItemsNames.push(item.name + ' ' + '(' + item.tid + ')');
    })
    var valuesString = selectedItemsNames.join(', '); // You can choose any separator you like

    let fieldId = '#edit-' + mutualId + '-target-id';

    $(fieldId).val(valuesString);
  });

  $('.select-term-to-query').on('click', (e) => {
    let buttonId = e.currentTarget.id;

    let mutualId = buttonId.substring(5, buttonId.length - 14);

    let treeId = '#' + mutualId + '_taxonomy_tree';

    let tree = $(treeId);

    let selectedItems = tree.jqxTreeGrid('getSelection');

    console.log(selectedItems);



  })



})(jQuery, Drupal, drupalSettings);
