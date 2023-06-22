<?php

use CRM_Civirfm_ExtensionUtil as E;

/*
 * Settings metadata file
 */

return [
  'civirfm_rfm_period' => [
    'name' => 'civirfm_rfm_period',
    'filter' => 'civirfm',
    'type' => 'String',
    'add' => '5.57',
    'is_contact' => 0,
    'description' => E::ts('The time period for calculating RFM values (in years)'),
    'title' => E::ts('RFM time period'),
    'default' => '5',
    'html_type' => 'text',
    'html_attributes' => [
      'size' => 5,
      'spellcheck' => 'false',
      'required' => 'true',
    ],
    'settings_pages' => ['civirfm' => ['weight' => 10]],
  ],
  'civirfm_fin_types' => [
    'name' => 'civirfm_fin_types',
    'filter' => 'civirfm',
    'type' => 'Integer',
    'add' => '5.57',
    'is_contact' => 0,
    'description' => E::ts('Contributions of these financial types will be used for RFM calculations'),
    'title' => E::ts('RFM Financial Types'),
    'html_type' => 'entity_reference',
    'html_attributes' => [
      'class' => 'crm-select2',
    ],
    'entity_reference_options' => [
      'entity' => 'FinancialType',
      'select' => ['minimumInputLength' => 0],
      'multiple' => true
    ],
    'settings_pages' => ['civirfm' => ['weight' => 20]],
  ]
];
