<?php

namespace Civi\Api4\Service\Spec\Provider;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;

class ContactRfmGetSpecProvider extends \Civi\Core\Service\AutoService implements Generic\SpecProviderInterface {

  /**
   * @param \Civi\Api4\Service\Spec\RequestSpec $spec
   */
  public function modifySpec(RequestSpec $spec) {
    // Recency field
    $field = new FieldSpec('recency', 'ContactRfm', 'Integer');
    $field->setLabel(ts('Recency (days)'))
      ->setTitle(ts('Recency (days)'))
      ->setColumnName('date_last_contrib')
      ->setInputType('Number')
      ->setDescription(ts('Time since last contribution (days)'))
      ->setType('Extra')
      ->setReadonly(TRUE)
      ->setSqlRenderer([__CLASS__, 'calculateRecency']);
    $spec->addFieldSpec($field);
  }

  /**
   * @param string $entity
   * @param string $action
   *
   * @return bool
   */
  public function applies($entity, $action) {
    return $entity === 'ContactRfm' && $action === 'get';
  }

  /**
   * Generate SQL for recency field
   * @param array $field
   * @return string
   */
  public static function calculateRecency(array $field): string {
    return "TIMESTAMPDIFF(DAY, {$field['sql_name']}, CURDATE())";
  }
}
