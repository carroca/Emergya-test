<?php

namespace Drupal\emergya_test\Entity\Node;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;

/**
 * Class for the Product node bundle.
 */
class Product extends Node {

  /**
   * {@inheritDoc}
   */
  public function access($operation = 'view', AccountInterface $account = NULL, $return_as_object = FALSE) {

    // Is not possible inject services in entities.
    /** @var \Drupal\emergya_test\Service\AccessCheckerInterface $accessChecker */
    $accessChecker = \Drupal::service('emergya_test.access_checker');

    if ('view' == $operation) {
      if (!$accessChecker->nodeProductAccess($this)) {
        return AccessResult::forbidden();
      }
    }

    return parent::access($operation, $account, $return_as_object);
  }

}
