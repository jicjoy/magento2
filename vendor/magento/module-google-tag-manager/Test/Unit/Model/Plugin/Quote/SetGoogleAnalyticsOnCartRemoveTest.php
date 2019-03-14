<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Model\Plugin\Quote;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\GoogleTagManager\Helper\Data as DataHelper;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\Quote as Quote;
use Magento\Framework\Registry;
use Magento\GoogleTagManager\Model\Plugin\Quote\SetGoogleAnalyticsOnCartRemove;

class SetGoogleAnalyticsOnCartRemoveTest extends \PHPUnit\Framework\TestCase
{
    /** @var SetGoogleAnalyticsOnCartRemove */
    private $model;

    /** @var QuoteItem|\PHPUnit_Framework_MockObject_MockObject */
    private $quoteItem;

    /** @var Quote|\PHPUnit_Framework_MockObject_MockObject */
    private $quote;

    /** @var ObjectManagerHelper */
    private $objectManagerHelper;

    /**
     * @var DataHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    protected function setUp()
    {
        $this->helper = $this->getMockBuilder(DataHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteItem = $this->getMockBuilder(QuoteItem::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQty'])
            ->getMock();

        $this->quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItemById'])
            ->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->setMethods(['register', 'unregister'])
            ->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            SetGoogleAnalyticsOnCartRemove::class,
            [
                'helper' => $this->helper,
                'registry' => $this->registry
            ]
        );
    }

    /**
     * @param $itemId
     * @param $buyRequest
     * @param $params
     *
     * @dataProvider updateItemDateProvider
     */
    public function testAfterUpdateItemWithQuoteItemQtyLessThanQuoteQty($itemId, $buyRequest, $params)
    {
        $proceed = function () use ($itemId, $buyRequest, $params) {
            return $this->quoteItem;
        };

        $this->quoteItem->expects($this->exactly(3))
            ->method('getQty')
            ->will($this->onConsecutiveCalls('2', '1'));

        $this->quote->expects($this->once())
            ->method('getItemById')
            ->willReturn($this->quoteItem);

        $this->helper->expects($this->once())
            ->method('isTagManagerAvailable')
            ->willReturn(true);

        $this->registry->expects($this->once())
            ->method('unregister');

        $this->registry->expects($this->once())
            ->method('register');

        $this->model->aroundUpdateItem($this->quote, $proceed, $itemId, $buyRequest, $params);
    }

    /**
     * @param $itemId
     * @param $buyRequest
     * @param $params
     *
     * @dataProvider updateItemDateProvider
     */
    public function testAfterUpdateItemWithQuoteItemQtyMoreThanQuoteQty($itemId, $buyRequest, $params)
    {
        $proceed = function () use ($itemId, $buyRequest, $params) {
            return $this->quoteItem;
        };

        $this->quoteItem->expects($this->exactly(2))
            ->method('getQty')
            ->will($this->onConsecutiveCalls('2', '3'));

        $this->quote->expects($this->once())
            ->method('getItemById')
            ->willReturn($this->quoteItem);

        $this->helper->expects($this->never())
            ->method('isTagManagerAvailable')
            ->willReturn(true);

        $this->registry->expects($this->never())
            ->method('unregister');

        $this->registry->expects($this->never())
            ->method('register');

        $this->model->aroundUpdateItem($this->quote, $proceed, $itemId, $buyRequest, $params);
    }

    public function testAfterRemoveItemWithQuoteItemQtyMoreThanQuoteQty()
    {
        $this->quoteItem->expects($this->exactly(1))
            ->method('getQty')
            ->willReturn(1);

        $this->quote->expects($this->once())
            ->method('getItemById')
            ->willReturn($this->quoteItem);

        $this->helper->expects($this->once())
            ->method('isTagManagerAvailable')
            ->willReturn(true);

        $this->registry->expects($this->once())
            ->method('unregister');

        $this->registry->expects($this->once())
            ->method('register');

        $this->model->afterRemoveItem($this->quote, $this->quoteItem, 1);
    }

    /**
     * @return array
     */
    public function updateItemDateProvider()
    {
        return [
            [1, false, null]
        ];
    }
}
