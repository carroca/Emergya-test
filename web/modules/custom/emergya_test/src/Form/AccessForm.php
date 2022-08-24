<?php

namespace Drupal\emergya_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\emergya_test\Service\AccessCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for the user access to some nodes.
 */
class AccessForm extends FormBase {

  /**
   * The service emergya_test.access_checker.
   *
   * @var \Drupal\emergya_test\Service\AccessCheckerInterface
   */
  private AccessCheckerInterface $accessChecker;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'emergya_test_access_form';
  }

  /**
   * Constructor of the form.
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
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('emergya_test.access_checker')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('I want access'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->accessChecker->allowSetCookieAccess();
    $this->messenger()->addMessage($this->t('Now you have access to the protected products'));
    //$form_state->setRedirect('<front>');
  }

}
