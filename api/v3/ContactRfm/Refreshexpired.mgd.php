<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'Cron:ContactRfm.Refreshexpired',
    'entity' => 'Job',
    'params' => [
      'version' => 3,
      'name' => 'CiviRFM find expired CiviRFM records',
      'description' => 'Checks for expired CiviRFM records and queues them for reprocessing',
      'run_frequency' => 'Daily',
      'api_entity' => 'ContactRfm',
      'api_action' => 'Refreshexpired',
      'parameters' => '',
    ],
  ],
];
