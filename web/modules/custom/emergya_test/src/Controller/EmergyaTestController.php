<?php

namespace Drupal\emergya_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Emergya Test routes.
 */
class EmergyaTestController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build(Request $request): RedirectResponse|array {

    // Don't allow access to the form again.
    if ($request->cookies->get('emergya_access', FALSE)) {
      $url = Url::fromRoute('<front>')->toString();
      return new RedirectResponse($url);
    }

    return $this->formBuilder()->getForm('Drupal\emergya_test\Form\AccessForm');
  }

}
