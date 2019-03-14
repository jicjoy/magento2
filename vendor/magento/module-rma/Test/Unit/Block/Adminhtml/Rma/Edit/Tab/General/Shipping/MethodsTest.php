<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Methods;
use Magento\Rma\Helper\Data as RmaHelperData;
use Magento\Rma\Model\Rma;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Tax\Helper\Data;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class MethodsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PriceCurrency|MockObject
     */
    private $priceCurrency;

    /**
     * @var Data|MockObject
     */
    private $taxData;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->priceCurrency = $this->getMockBuilder(PriceCurrency::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->taxData = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShippingPrice'])
            ->getMock();
    }

    /**
     * Checks a case when shipping methods which are not allow to create shipping labels should be skipped.
     */
    public function testGetShippingLabelAvailableMethods()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $jsonEncoder = $this->getMockBuilder(EncoderInterface::class)
            ->getMockForAbstractClass();
        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rmaHelperData = $this->getMockBuilder(RmaHelperData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->initShippingMethodMockList($registry);
        $this->initCarrierMockList($rmaHelperData);

        $methods = new Methods(
            $context,
            $jsonEncoder,
            $this->taxData,
            $registry,
            $this->priceCurrency,
            [],
            $rmaHelperData
        );

        $actual = $methods->getShippingMethods();
        self::assertEquals(1, sizeof($actual), 'Only one shipping method should be available.');

        /** @var Rate|MockObject $availableMethod */
        $availableMethod = array_pop($actual);
        self::assertEquals('carrier2_method1', $availableMethod->getCode());
    }

    /**
     * Initializes mocks for available shipping carriers.
     *
     * @param RmaHelperData|MockObject $rmaHelperData
     */
    private function initCarrierMockList(RmaHelperData $rmaHelperData)
    {
        $carrier1 = $this->getMockBuilder(AbstractCarrierOnline::class)
            ->disableOriginalConstructor()
            ->getMock();
        $carrier1->method('isShippingLabelsAvailable')
            ->willReturn(false);

        $carrier2 = $this->getMockBuilder(AbstractCarrierOnline::class)
            ->disableOriginalConstructor()
            ->getMock();
        $carrier2->method('isShippingLabelsAvailable')
            ->willReturn(true);

        $rmaHelperData->method('getCarrier')
            ->willReturnMap([
                ['carrier1_method1', 0, $carrier1],
                ['carrier2_method1', 0, $carrier2],
                ['carrier3_method1', 0, false],
            ]);
    }

    /**
     * Initializes mocks for shipping methods.
     *
     * @param Registry|MockObject $registry
     */
    private function initShippingMethodMockList(Registry $registry)
    {
        $rmaModel = $this->getMockBuilder(Rma::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registry->method('registry')
            ->with('current_rma')
            ->willReturn($rmaModel);

        $methods = [
            $this->createShippingMethodMock('carrier1_method1'),
            $this->createShippingMethodMock('carrier1'),
            $this->createShippingMethodMock('carrier1_method2_additional'),
            $this->createShippingMethodMock('carrier2_method1'),
            $this->createShippingMethodMock('carrier3_method1'),
        ];

        $rmaModel->method('getShippingMethods')
            ->willReturn($methods);
        $rmaModel->method('getStoreId')
            ->willReturn(null);
    }

    /**
     * Creates mock for shipping method object.
     *
     * @param string $code
     * @return Rate|MockObject
     */
    private function createShippingMethodMock(string $code)
    {
        $method = $this->getMockBuilder(Rate::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCode'])
            ->getMock();
        $method->method('getCode')
            ->willReturn($code);

        return $method;
    }

    public function testGetShippingPrice()
    {
        $methods = $this->objectManager->getObject(
            Methods::class,
            [
                'taxData' => $this->taxData,
                'priceCurrency' => $this->priceCurrency
            ]
        );

        $price = 100;
        $expected = 100.00;

        $this->taxData->method('getShippingPrice')
            ->with($price)
            ->willReturnArgument(0);
        $this->priceCurrency->method('convert')
            ->with($price, true, false)
            ->willReturn($expected);
        $this->assertEquals($expected, $methods->getShippingPrice($price));
    }
}
