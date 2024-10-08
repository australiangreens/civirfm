<?php
use CRM_Civirfm_ExtensionUtil as E;
return [
  'name' => 'ContactRfm',
  'table' => 'civicrm_contact_rfm',
  'class' => 'CRM_Civirfm_DAO_ContactRfm',
  'getInfo' => fn() => [
    'title' => E::ts('Contact Rfm'),
    'title_plural' => E::ts('Contact Rfms'),
    'description' => E::ts('RFM data for CiviCRM contacts'),
    'log' => TRUE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('Unique ContactRfm ID'),
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'contact_id' => [
      'title' => E::ts('Contact ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'description' => E::ts('FK to Contact - should be unique within table'),
      'entity_reference' => [
        'entity' => 'Contact',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'date_last_contrib' => [
      'title' => E::ts('Date Last Contrib'),
      'sql_type' => 'timestamp',
      'input_type' => NULL,
      'description' => E::ts('Date of last relevant contribution'),
    ],
    'date_first_contrib' => [
      'title' => E::ts('Date First Contrib'),
      'sql_type' => 'timestamp',
      'input_type' => NULL,
      'description' => E::ts('Date of first relevant contribution'),
    ],
    'date_calculated' => [
      'title' => E::ts('Date Calculated'),
      'sql_type' => 'timestamp',
      'input_type' => NULL,
      'description' => E::ts('Date of last calculation of RFM values'),
    ],
    'frequency' => [
      'title' => E::ts('Frequency'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'description' => E::ts('Number of gifts in RFM period'),
    ],
    'monetary' => [
      'title' => E::ts('Monetary'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'description' => E::ts('Average gift value in RFM period'),
    ],
  ],
];
