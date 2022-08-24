<?php

namespace Drupal\emergya_test\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service to check the access to some sections.
 */
class AccessChecker implements AccessCheckerInterface {

  /**
   * The service current_user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private AccountProxyInterface $accountProxy;

  /**
   * The current request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  private Request $request;

  /**
   * Store if the cookie should be set.
   *
   * @var bool
   */
  private bool $allowCookieAccess;

  /**
   * The constructor of the service.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $accountProxy
   *   The service current_user.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The service request_stack.
   */
  public function __construct(AccountProxyInterface $accountProxy, RequestStack $requestStack) {
    $this->accountProxy = $accountProxy;
    $this->request = $requestStack->getCurrentRequest();
    $this->allowCookieAccess = FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function nodeProductAccess(NodeInterface $node): bool {

    if ($this->accountProxy->hasPermission('bypass emergya_test products')) {
      return TRUE;
    }

    // If the cookies is set, allow the access.
    if ($this->request->cookies->get('emergya_access', FALSE)) {
      return TRUE;
    }

    if ('product' == $node->bundle()) {

      if ($node->get('field_protected')->isEmpty()) {
        return TRUE;
      }

      if ($node->get('field_protected')->value) {
        return FALSE;
      }

    }

    // By default, allow access;
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function allowSetCookieAccess(): void {
    $this->allowCookieAccess = TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function isCookieSetAllowed(): bool {
    return $this->allowCookieAccess;
  }

}
