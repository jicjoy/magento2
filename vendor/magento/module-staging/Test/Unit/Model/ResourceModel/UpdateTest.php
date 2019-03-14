<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\ResourceModel;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Tests for the \Magento\Staging\Model\ResourceModel\Update class
 */
class UpdateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Staging\Model\ResourceModel\Update
     */
    private $update;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resources;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resources = $this->getMockBuilder(\Magento\Framework\App\ResourceConnection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConnection', 'getTableName'])
            ->getMock();

        $this->update = $objectManager->getObject(
            \Magento\Staging\Model\ResourceModel\Update::class,
            [
                'resources' => $this->resources
            ]
        );
    }

    /**
     * Test isRollbackAssignedToUpdates() method with assigned to update rollback
     */
    public function testIsRollbackAssignedToUpdatesWithAssignedId()
    {
        $rollbackId = 123;
        $updateId = 321;
        $defaultTableName = 'default_table';
        $connection = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->setMethods(['select', 'fetchOne'])
            ->getMockForAbstractClass();
        $this->resources->expects($this->atLeastOnce())->method('getConnection')
            ->with(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->willReturn($connection);
        $this->resources->expects($this->atLeastOnce())->method('getTableName')->willReturn($defaultTableName);
        $select = $this->getMockBuilder(\Magento\Framework\DB\Select::class)
            ->disableOriginalConstructor()
            ->setMethods(['from', 'where', 'limit'])
            ->getMock();
        $select->expects($this->atLeastOnce())->method('from')->with($defaultTableName)->willReturnSelf();
        $select->expects($this->exactly(2))->method('where')->withConsecutive(
            ['rollback_id = ?', $rollbackId],
            ['id NOT IN (?)', [$updateId]]
        )->willReturnSelf();
        $select->expects($this->atLeastOnce())->method('limit')->with(1)->willReturnSelf();
        $connection->expects($this->atLeastOnce())->method('select')->willReturn($select);
        $connection->expects($this->atLeastOnce())->method('fetchOne')->willReturn('string');

        $this->assertEquals(true, $this->update->isRollbackAssignedToUpdates($rollbackId, [$updateId]));
    }

    /**
     * Test isRollbackAssignedToUpdates() method without assigned to update rollback
     */
    public function testIsRollbackAssignedToUpdatesWithoutAssignedId()
    {
        $rollbackId = 123;
        $updateId = 321;
        $defaultTableName = 'default_table';
        $connection = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->setMethods(['select', 'fetchOne'])
            ->getMockForAbstractClass();
        $this->resources->expects($this->atLeastOnce())->method('getConnection')
            ->with(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->willReturn($connection);
        $this->resources->expects($this->atLeastOnce())->method('getTableName')->willReturn($defaultTableName);
        $select = $this->getMockBuilder(\Magento\Framework\DB\Select::class)
            ->disableOriginalConstructor()
            ->setMethods(['from', 'where', 'limit'])
            ->getMock();
        $select->expects($this->atLeastOnce())->method('from')->with($defaultTableName)->willReturnSelf();
        $select->expects($this->exactly(2))->method('where')->withConsecutive(
            ['rollback_id = ?', $rollbackId],
            ['id NOT IN (?)', [$updateId]]
        )->willReturnSelf();
        $select->expects($this->atLeastOnce())->method('limit')->with(1)->willReturnSelf();
        $connection->expects($this->atLeastOnce())->method('select')->willReturn($select);
        $connection->expects($this->atLeastOnce())->method('fetchOne')->willReturn(false);

        $this->assertEquals(false, $this->update->isRollbackAssignedToUpdates($rollbackId, [$updateId]));
    }
}
