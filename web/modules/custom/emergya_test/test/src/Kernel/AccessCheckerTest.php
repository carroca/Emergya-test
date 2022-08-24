<?php

namespace Drupal\Tests\emergya_test\Kernel;

use Drupal\emergya_test\Service\AccessChecker;
use Drupal\emergya_test\Service\AccessCheckerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\Entity\NodeType as EntityNodeType;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Test the service occhio_language.country_access.
 */
class AccessCheckerTest extends EntityKernelTestBase {

  use NodeCreationTrait;
  use EntityReferenceTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'taxonomy',
    'emergya_test',
    'node',
    'user',
    'system',
    'field',
    'text',
    'filter',
    'entity_test',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('node', ['node_access']);
    $this->installConfig(['filter']);

    $node_type = EntityNodeType::create([
      'type' => 'product',
      'label' => 'Product',
    ]);
    $node_type->save();

    $this->addProtectedField('node', 'product');
  }

  /**
   * Few checks to test the access check to nodes.
   */
  public function testAccessIsAllowed() {

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'page',
      'title' => 'ProductTest',
    ]);

    $accessChecker = $this->prepareCookieCheckService(FALSE);

    // Default option should be returned because is not a product node.
    $this->assertTrue($accessChecker->nodeProductAccess($node));

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'product',
      'title' => 'ProductTest',
    ]);

    $accessChecker = $this->prepareBypassService();

    // The user has bypass permissions.
    $this->assertTrue($accessChecker->nodeProductAccess($node));

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'product',
      'title' => 'ProductTest',
    ]);

    $accessChecker = $this->prepareCookieCheckService(TRUE);

    // Here the user don't have permissions.
    $this->assertTrue($accessChecker->nodeProductAccess($node));

    $accessChecker = $this->prepareCookieCheckService(FALSE);

    // The cookie is set, should return TRUE.
    $this->assertTrue($accessChecker->nodeProductAccess($node));

    $node->set('field_protected', TRUE);

    // The node is protected, should return FALSE.
    $this->assertFalse($accessChecker->nodeProductAccess($node));
  }

  /**
   * Test the cookie allowed.
   */
  public function testCookieCheck() {
    $accessChecker = $this->prepareCookieCheckService(FALSE);

    // By default, the value is FALSE.
    $this->assertFalse($accessChecker->isCookieSetAllowed());

    // Set to TRUE to return TRUE.
    $accessChecker->allowSetCookieAccess();
    $this->assertTrue($accessChecker->isCookieSetAllowed());
  }

  /**
   * Creates the service emergya_test.access_checker with one user that has
   *   permissions to bypass the check.
   *
   * @return \Drupal\emergya_test\Service\AccessCheckerInterface
   *   The service emergya_test.access_checker manually created.
   */
  public function prepareBypassService(): AccessCheckerInterface {
    $user = $this->drupalCreateUser(['bypass emergya_test products']);

    $request = new Request();
    $request->cookies->set('emergya_access', TRUE);

    $requestStack = new RequestStack();
    $requestStack->push($request);

    /** @var \Drupal\Core\Session\AccountProxyInterface $accountProxy */
    $accountProxy = $this->container->get('current_user');

    $accountProxy->setAccount($user);

    return new AccessChecker($accountProxy, $requestStack);
  }

  /**
   * Creates the service emergya_test.access_checker with different values in
   *   the cookie and user without permissions.
   *
   * @param bool $cookie
   *   The value of the cookie.
   *
   * @return \Drupal\emergya_test\Service\AccessCheckerInterface
   *   The service emergya_test.access_checker manually created.
   */
  public function prepareCookieCheckService(bool $cookie = FALSE): AccessCheckerInterface {
    $user = $this->drupalCreateUser();

    $request = new Request();
    $request->cookies->set('emergya_access', $cookie);

    $requestStack = new RequestStack();
    $requestStack->push($request);

    /** @var \Drupal\Core\Session\AccountProxyInterface $accountProxy */
    $accountProxy = $this->container->get('current_user');

    $accountProxy->setAccount($user);

    return new AccessChecker($accountProxy, $requestStack);
  }

  /**
   * Add necessary fields to the entity type for the test.
   *
   * @param $entity_type
   *   The entity type.
   * @param $bundle
   *   The bundle of the entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function addProtectedField($entity_type, $bundle): void {
    $field_storage = FieldStorageConfig::loadByName($entity_type, 'field_protected');
    if (!$field_storage) {
      // Add the field.
      $field_storage = FieldStorageConfig::create([
        'field_name' => 'field_protected',
        'entity_type' => $entity_type,
        'type' => 'boolean',
        'cardinality' => '1',
      ]);
      $field_storage->save();
    }
    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $bundle,
      'settings' => [],
    ]);
    $field->save();
  }

}
