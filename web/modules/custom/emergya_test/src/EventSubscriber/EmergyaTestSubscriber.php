<?php

namespace Drupal\emergya_test\EventSubscriber;

use Drupal\emergya_test\Service\AccessCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Emergya Test event subscriber to check the response.
 */
class EmergyaTestSubscriber implements EventSubscriberInterface {

  /**
   * The service emergya_test.access_checker.
   *
   * @var \Drupal\emergya_test\Service\AccessCheckerInterface
   */
  private AccessCheckerInterface $accessChecker;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\emergya_test\Service\AccessCheckerInterface $accessChecker
   *   The service emergya_test.access_checker.
   */
  public function __construct(AccessCheckerInterface $accessChecker) {
    $this->accessChecker = $accessChecker;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

  /**
   * Check if the cookie must be set.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   Response event.
   */
  public function onKernelResponse(ResponseEvent $event) {
    if ($this->accessChecker->isCookieSetAllowed()) {
      $response = $event->getResponse();
      // Set the cookie for 1 day.
      $response->headers->setCookie(new Cookie('emergya_access', TRUE, time() + 86400));
    }
  }

}
