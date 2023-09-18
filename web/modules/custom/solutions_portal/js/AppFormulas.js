/**
 * @file
 * Solutions Portal behaviors.
 */
(function (Drupal) {

  'use strict';

  const getElement = (elementID) => {
    return document.getElementById(elementID);
  };

  const getValue = (elementID) => {
    return document.getElementById(elementID).value;
  };

  const setValue = (elementID, value) => {
    document.getElementById(elementID).value = value;
  };

  const formatDecimal = (f) => {
    f = f.toFixed(5);
    let str = '' + f;
    str = str.replace(/0*$/, '');
    if (str.lastIndexOf('.') == str.length - 1) {
      str = str.substr(0, str.length - 1);
    }

    return str;
  };

  Drupal.behaviors.solutionsPortalSolutionsPortal = {
    attach(context, settings) {
      if (getElement('edit-field-is-terolink') === null || getValue('edit-field-is-terolink') === '2') {
        const calculateCO2Reduction = () => {
          let weightInKG = parseFloat(getValue('edit-field-weight-0-value'));
          let lpf = getValue('edit-field-lpf-0-value');
          let co2ReductionFactor = getValue('edit-field-co2reductionfactor-0-value');

          let co2Reduction = (weightInKG / 1000) * lpf * co2ReductionFactor;

          co2Reduction = isNaN(co2Reduction) ? 0 : co2Reduction.toFixed(2);

          setValue('edit-field-co2reduction-0-value', co2Reduction);
        };
        const calculateTotalLife = (prefix) => {
          let eco_partlife_value = parseFloat(getValue(`edit-field-eco-${prefix}-partlife-0-value`));
          let eco_possibletreatments_value = parseFloat(getValue(`edit-field-eco-${prefix}-possibletreatments-0-value`));
          let eco_totallife_value = 0;

          if (eco_partlife_value != null && eco_possibletreatments_value === 0) {
            eco_totallife_value = eco_partlife_value * (eco_possibletreatments_value + 1);
          } else {
            eco_totallife_value = eco_partlife_value * eco_possibletreatments_value;
          }

          setValue(`edit-field-eco-${prefix}-totallife-0-value`, eco_totallife_value);
          calculateECOTest();
        };

        // reactions on user input
        getElement('edit-field-eco-fs-partlife-0-value').addEventListener('keyup', () => {
          calculateTotalLife('fs');
        });
        getElement('edit-field-eco-fs-possibletreatments-0-value').addEventListener('keyup', () => {
          calculateTotalLife('fs');
        });
        getElement('edit-field-eco-cs-partlife-0-value').addEventListener('keyup', () => {
          calculateTotalLife('cs');
        });
        getElement('edit-field-eco-cs-possibletreatments-0-value').addEventListener('keyup', () => {
          calculateTotalLife('cs');
        });

        const calculateECOTestOnFieldChange = (elementID) => {
          getElement(elementID).addEventListener('change', () => {
            calculateECOTest();
          });
        };
        const listOfFieldsToCalculateOnFieldChange = [
          'edit-field-eco-numberofparts-0-value',
          'edit-field-eco-fs-totallife-0-value',
          'edit-field-eco-fs-newpartunitprice-0-value',
          'edit-field-eco-fs-unitcostoftreatment-0-value',
          'edit-field-eco-fs-nrassemblybypart-0-value',
          'edit-field-eco-fs-unitcostassembly-0-value',
          'edit-field-eco-fs-nrofmonthperyear-0-value',
          'edit-field-eco-cs-totallife-0-value',
          'edit-field-eco-cs-newpartunitprice-0-value',
          'edit-field-eco-cs-unitcostoftreatment-0-value',
          'edit-field-eco-cs-nrassemblybypart-0-value',
          'edit-field-eco-cs-unitcostassembly-0-value',
          'edit-field-eco-cs-nrofmonthperyear-0-value',
        ];

        listOfFieldsToCalculateOnFieldChange.forEach(field => calculateECOTestOnFieldChange(field));

        getElement('edit-field-co2reductionfactor-0-value').addEventListener('change', calculateCO2Reduction);

        const calculateECOTest = () => {
          let numberOfPartsCopies = document.querySelectorAll('[id*=\'edit-field-eco-numberofparts-wrapper--\']');
          for (let i = 0; i < numberOfPartsCopies.length; i++) {
            setValue(numberOfPartsCopies[i].children[0].children[1].attributes[3].value, getValue('edit-field-eco-numberofparts-0-value'));
          }

          setValue('edit-field-eco-fs-totallife-1-0-value', formatDecimal(parseFloat(getValue('edit-field-eco-fs-totallife-0-value')) / 30));
          setValue('edit-field-eco-cs-totallife-1-0-value', formatDecimal(parseFloat(getValue('edit-field-eco-cs-totallife-0-value')) / 30));

          setValue('edit-field-eco-fs-newpartscost-0-value', (parseFloat(getValue('edit-field-eco-numberofparts-0-value')) * parseFloat(getValue('edit-field-eco-fs-newpartunitprice-0-value'))).toFixed(2));
          setValue('edit-field-eco-fs-maintenancecost-0-value', (parseFloat(getValue('edit-field-eco-fs-possibletreatments-0-value')) * parseFloat(getValue('edit-field-eco-numberofparts-0-value')) * parseFloat(getValue('edit-field-eco-fs-unitcostoftreatment-0-value'))).toFixed(2));
          setValue('edit-field-eco-fs-costassembly-0-value', (parseFloat(getValue('edit-field-eco-fs-unitcostassembly-0-value')) * parseFloat(getValue('edit-field-eco-numberofparts-0-value')) * parseFloat(getValue('edit-field-eco-fs-nrassemblybypart-0-value'))).toFixed(2));
          setValue('edit-field-eco-fs-totallifecost-0-value', (parseFloat(getValue('edit-field-eco-fs-newpartscost-0-value')) + parseFloat(getValue('edit-field-eco-fs-maintenancecost-0-value')) + parseFloat(getValue('edit-field-eco-fs-costassembly-0-value'))).toFixed(2));
          setValue('edit-field-eco-fs-annualoperatingcost-0-value', ((parseFloat(getValue('edit-field-eco-fs-nrofmonthperyear-0-value')) / parseFloat(getValue('edit-field-eco-fs-totallife-0-value'))) * parseFloat(getValue('edit-field-eco-fs-totallifecost-0-value'))).toFixed(2));

          setValue('edit-field-eco-cs-newpartscost-0-value', (parseFloat(getValue('edit-field-eco-numberofparts-0-value')) * parseFloat(getValue('edit-field-eco-cs-newpartunitprice-0-value'))).toFixed(2));
          setValue('edit-field-eco-cs-maintenancecost-0-value', (parseFloat(getValue('edit-field-eco-cs-possibletreatments-0-value')) * parseFloat(getValue('edit-field-eco-numberofparts-0-value')) * parseFloat(getValue('edit-field-eco-cs-unitcostoftreatment-0-value'))).toFixed(2));
          setValue('edit-field-eco-cs-costassembly-0-value', (parseFloat(getValue('edit-field-eco-cs-unitcostassembly-0-value')) * parseFloat(getValue('edit-field-eco-numberofparts-0-value')) * parseFloat(getValue('edit-field-eco-cs-nrassemblybypart-0-value'))).toFixed(2));
          setValue('edit-field-eco-cs-totallifecost-0-value', (parseFloat(getValue('edit-field-eco-cs-newpartscost-0-value')) + parseFloat(getValue('edit-field-eco-cs-maintenancecost-0-value')) + parseFloat(getValue('edit-field-eco-cs-costassembly-0-value'))).toFixed(2));
          setValue('edit-field-eco-cs-annualoperatingcost-0-value', ((parseFloat(getValue('edit-field-eco-cs-nrofmonthperyear-0-value')) / parseFloat(getValue('edit-field-eco-cs-totallife-0-value'))) * parseFloat(getValue('edit-field-eco-cs-totallifecost-0-value'))).toFixed(2));

          setValue('edit-field-eco-cost-reduction-0-value', (parseFloat(getValue('edit-field-eco-fs-annualoperatingcost-0-value')) - parseFloat(getValue('edit-field-eco-cs-annualoperatingcost-0-value'))).toFixed(2));
          setValue('edit-field-lpf-0-value', (parseFloat(getValue('edit-field-eco-cs-totallife-0-value')) / parseFloat(getValue('edit-field-eco-fs-totallife-0-value'))).toFixed(2));

          calculateCO2Reduction();
        };

        calculateECOTest();
        calculateTotalLife('fs');
        calculateTotalLife('cs');
      }
    },
  };

}(Drupal));
