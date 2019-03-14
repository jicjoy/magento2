<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GiftCardAccount\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\GiftCardAccount\Observer\ReturnFundsToStoreCredit;
use Magento\GiftCardAccount\Observer\RevertGiftCardAccountBalance;
use Magento\GiftCardAccount\Observer\SalesOrderCancelAfter;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test for Magento\GiftCardAccount\Observer\SalesOrderCancelAfterTest class.
 */
class SalesOrderCancelAfterTest extends \PHPUnit\Framework\TestCase
{
    /** @var SalesOrderCancelAfter */
    private $model;

    /**
     * @var ReturnFundsToStoreCredit|MockObject
     */
    private $returnFundsToStoreCreditMock;

    /**
     * @var RevertGiftCardAccountBalance|MockObject
     */
    private $revertGiftCardAccountBalanceMock;

    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->returnFundsToStoreCreditMock = $this->getMockBuilder(ReturnFundsToStoreCredit::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->revertGiftCardAccountBalanceMock = $this->getMockBuilder(RevertGiftCardAccountBalance::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManagerHelper->getObject(
            SalesOrderCancelAfter::class,
            [
                'returnFundsToStoreCredit' => $this->returnFundsToStoreCreditMock,
                'revertGiftCardAccountBalance' => $this->revertGiftCardAccountBalanceMock,
            ]
        );
    }

    /**
     * @param int|null $customerId
     * @return void
     * @dataProvider executeDataProvider
     */
    public function testExecute($customerId)
    {
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder'])
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $this->observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $this->returnFundsToStoreCreditMock->expects($this->exactly($customerId ? 1 : 0))
            ->method('execute');
        $this->revertGiftCardAccountBalanceMock->expects($this->exactly($customerId ? 0 : 1))
            ->method('execute')
            ->with($this->observerMock);

        $this->model->execute($this->observerMock);
    }

    /**
     * @return array
     */
    public function executeDataProvider() : array
    {
        return [[1], [null]];
    }
}
