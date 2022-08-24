<?php

namespace Drupal\emergya_test\EventSubscriber;

use Drupal\Core\EventSubscriber\HttpExceptionSubscriberBase;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Check is the user must be redirected on 403 exceptions.
 */
class EmergyaProductAccessSubscriber extends HttpExceptionSubscriberBase {

  /**
   * The service current_route_match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private CurrentRouteMatch $currentRouteMatch;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   *   The service current_route_match.
   */
  public function __construct(CurrentRouteMatch $currentRouteMatch ) {
    $this->currentRouteMatch = $currentRouteMatch;
  }

  /**
   * {@inheritdoc}
   */
  protected function getHandledFormats() {
    return ['html'];
  }

  /**
   * Redirects on 403 Access Denied kernel exceptions if the exception comes
   *   from the node product type.
   *
   * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
   *   The Event to process.
   */
  public function on403(ExceptionEvent $event) {

    $request = $event->getRequest();

    if ('entity.node.canonical' == $this->currentRouteMatch->getRouteName() && $node = $request->attributes->get('node')) {
      if ('product' == $node->bundle()) {
        $url = Url::fromRoute('emergya_test.access_form');
        $url->setOptions(['query' => ['destination' => $request->getPathInfo()]]);
        $response = new RedirectResponse($url->toString());
        $event->setResponse($response);
      }
    }

  }

}
