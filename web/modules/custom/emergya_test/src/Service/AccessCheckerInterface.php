<?php

namespace Drupal\emergya_test\Service;

use Drupal\node\NodeInterface;

/**
 * Interface for the service that check the access to nodes.
 */
interface AccessCheckerInterface {

  /**
   * Check if the user has permission to access to one node.
   *
   * @return bool
   *   Return TRUE if the user can access to the node.
   */
  public function nodeProductAccess(NodeInterface $node): bool;

  /**
   * Allow set the cookie.
   */
  public function allowSetCookieAccess(): void;

  /**
   * Check if the cookie is allowed to set.
   *
   * @return bool
   *  The current state of the cookie allowed.
   */
  public function isCookieSetAllowed(): bool;
}
