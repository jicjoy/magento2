<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\ResourceModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IndexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TargetRule\Model\ResourceModel\Index
     */
    private $model;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapterInterface;

    protected function setUp()
    {
        $contextMock = $this->createMock(\Magento\Framework\Model\ResourceModel\Db\Context::class);
        $indexPoolMock = $this->createMock(\Magento\TargetRule\Model\ResourceModel\IndexPool::class);
        $ruleMock = $this->createMock(\Magento\TargetRule\Model\ResourceModel\Rule::class);
        $segmentCollectionFactoryMock = $this->createMock(
            \Magento\CustomerSegment\Model\ResourceModel\Segment::class
        );
        $productCollectionFactoryMock = $this->createMock(
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class
        );
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $visibilityMock = $this->createMock(\Magento\Catalog\Model\Product\Visibility::class);
        $customerMock = $this->createMock(\Magento\CustomerSegment\Model\Customer::class);
        $sessionMock = $this->createMock(\Magento\Customer\Model\Session::class);
        $customerSegmentDataMock = $this->createMock(\Magento\CustomerSegment\Helper\Data::class);
        $targetRuleDataMock = $this->createMock(\Magento\TargetRule\Helper\Data::class);
        $stockHelperMock = $this->createMock(\Magento\CatalogInventory\Helper\Stock::class);

        $this->adapterInterface = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $resourceMock = $this->createMock(\Magento\Framework\App\ResourceConnection::class);
        $resourceMock->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->adapterInterface);

        $contextMock->expects($this->any())
            ->method('getResources')
            ->willReturn($resourceMock);

        $this->model = (new ObjectManager($this))->getObject(
            \Magento\TargetRule\Model\ResourceModel\Index::class,
            [
                'context' => $contextMock,
                'indexPool' => $indexPoolMock,
                'ruleMock' => $ruleMock,
                'segmentCollectionFactory' => $segmentCollectionFactoryMock,
                'productCollectionFactory' => $productCollectionFactoryMock,
                'storeManager' => $storeManagerMock,
                'visibility' => $visibilityMock,
                'customer' => $customerMock,
                'session' => $sessionMock,
                'customerSegmentData' => $customerSegmentDataMock,
                'targetRuleData' => $targetRuleDataMock,
                'coreRegistry' => $this->createMock(\Magento\Framework\Registry::class),
                'stockHelper' => $stockHelperMock,
            ]
        );
    }

    /**
     * @return array
     */
    public function getOperatorConditionDataProvider()
    {
        return [
            ['category_id', '()', ' IN(?)', [4], [4]],
            ['category_id', '!()', ' NOT IN(?)', [4], [4]],
            ['category_id', '{}', ' IN (?)', [5], [5]],
            ['category_id', '!{}', ' NOT IN (?)', [5], [5]],
            ['category_id', '{}', ' LIKE ?', 5, '%5%'],
            ['category_id', '!{}', ' NOT LIKE ?', 5, '%5%'],
            ['category_id', '>=', '>=?', 5, 5],
            ['category_id', '==', '=?', 7, 7],
        ];
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string $expectedSelectOperator
     * @param mixed $value
     * @param mixed $expectedValue
     *
     * @dataProvider getOperatorConditionDataProvider
     */
    public function testGetOperatorCondition($field, $operator, $expectedSelectOperator, $value, $expectedValue)
    {
        $quoteIdentifier = '`' . $field . '`';
        $this->adapterInterface->expects($this->once())
            ->method('quoteIdentifier')
            ->willReturn($quoteIdentifier);

        $this->adapterInterface->expects($this->once())
            ->method('quoteInto')
            ->with($this->equalTo($quoteIdentifier . $expectedSelectOperator), $this->equalTo($expectedValue));

        $this->model->getOperatorCondition($field, $operator, $value);
    }
}
