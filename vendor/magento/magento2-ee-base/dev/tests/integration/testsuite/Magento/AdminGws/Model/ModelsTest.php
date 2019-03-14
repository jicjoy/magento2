<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdminGws\Model;

use Magento\Authorization\Model\Role;
use Magento\AdminGws\Model\Role as AdminGwsRole;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Authorization\Model\RoleFactory;

/**
 * @magentoAppArea adminhtml
 */
class ModelsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var RoleFactory
     */
    private $roleFactory;

    /**
     * @var AdminGwsRole
     */
    private $role;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->customerRepository = $this->objectManager->get(CustomerRepositoryInterface::class);
        $this->websiteRepository = $this->objectManager->get(WebsiteRepositoryInterface::class);
        $this->roleFactory = $this->objectManager->get(RoleFactory::class);
        $this->role = $this->objectManager->get(AdminGwsRole::class);
    }

    protected function tearDown()
    {
        /* @var Role $role */
        $role = $this->roleFactory->create();
        $role = $role->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->role->setAdminRole($role);
    }

    /**
     * Tests case when admin has User Role with access only to one website
     * and tries to update product which is assigned to two websites.
     * Attributes from global scope level has to be locked for changes and
     * attributes from website/store view level should be available for updates.
     *
     * @covers \Magento\AdminGws\Model\Models::catalogProductLoadAfter
     * @magentoDataFixture Magento/Catalog/_files/product_with_two_websites.php
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     */
    public function testCatalogProductLoadAfter()
    {
        /** @var Role $adminRole */
        $adminRole = $this->objectManager->get(Role::class);
        $adminRole->load('admingws_role', 'role_name');

        /** @var \Magento\AdminGws\Model\Role $adminGwsRole */
        $adminGwsRole = $this->objectManager->get(AdminGwsRole::class);
        $adminGwsRole->setAdminRole($adminRole);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('unique-simple-azaza', true);

        $origProductName = $product->getName();
        $newProductName = $origProductName . ' new';

        $origPrice = $product->getPrice();
        $newPrice = $origPrice + 1;

        $origStatus = $product->getStatus();
        $newStatus = $origStatus ? 0 : 1;

        $product->setName($newProductName);
        $product->setPrice($newPrice);
        $product->setStatus($newStatus);

        $this->assertEquals(
            $newProductName,
            $product->getName(),
            'Attribute from store view scope should be available for update'
        );

        $this->assertEquals(
            $newStatus,
            $product->getStatus(),
            'Attribute from website scope should be available for update'
        );

        $this->assertEquals(
            $origPrice,
            $product->getPrice(),
            'Attribute from global scope should be locked for update'
        );
    }

    /**
     * Test restrictions applying to updating customers.
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     * @magentoDataFixture Magento/Store/_files/website.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage You need more permissions to save this item.
     */
    public function testCustomerSave()
    {
        /** @var Role $role */
        $role = $this->roleFactory->create();
        $role = $role->load('admingws_role', 'role_name');
        //Setting role's scope to test website.
        $testWebsite = $this->websiteRepository->get('test');
        $role->setGwsIsAll(0);
        $role->setGwsWebsites([$testWebsite->getId()]);
        $role->setGwsRelevantWebsites([(int)$testWebsite->getId()]);
        $this->role->setAdminRole($role);

        //Saving customer from restricted website.
        $customer = $this->customerRepository->get('customer@example.com');
        $customer->setWebsiteId($testWebsite->getId());
        $this->customerRepository->save($customer);
    }
}
