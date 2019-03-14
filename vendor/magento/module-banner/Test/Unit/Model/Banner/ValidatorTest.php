<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Test\Unit\Model\Banner;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Banner\Model\Banner\Validator
     */
    protected $bannerValidator;

    /**
     * @var \Magento\Store\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Backend\Helper\Js|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $jsHelperMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jsHelperMock = $this->getMockBuilder(\Magento\Backend\Helper\Js::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->bannerValidator = $objectManager->getObject(
            \Magento\Banner\Model\Banner\Validator::class,
            [
                'storeManager' => $this->storeManagerMock,
                'jsHelper' => $this->jsHelperMock
            ]
        );
    }

    /**
     * @param array $data
     * @param array $currentStores
     * @param array $expected
     *
     * @dataProvider prepareSaveDataDataProvider
     */
    public function testPrepareSaveData($data, $currentStores, $expected)
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStores')
            ->with(true)
            ->willReturn($currentStores);

        $this->jsHelperMock->expects($this->any())
            ->method('decodeGridSerializedInput')
            ->willReturnMap(
                [
                    ['banner_catalog_rules_encoded', ['11', '22']],
                    ['banner_sales_rules_encoded', ['33', '44']]
                ]
            );

        $this->assertSame(
            $expected,
            $this->bannerValidator->prepareSaveData($data)
        );
    }

    /**
     * @return array
     */
    public function prepareSaveDataDataProvider()
    {
        return [
            [
                'data' => [
                    'store_contents_not_use' => ['store0' => 'store0', 'store1' => 'store1', 'store3' => 'store3'],
                    'store_contents' => ['store2' => 'store2'],
                    'banner_catalog_rules' => 'banner_catalog_rules_encoded',
                    'banner_sales_rules' => 'banner_sales_rules_encoded',
                ],
                'currentStores' => ['store2' => 'store2', 'store3' => 'store3'],
                'expected' => [
                    'store_contents_not_use' => ['store3' => 'store3'],
                    'store_contents' => ['store2' => 'store2'],
                    'banner_catalog_rules' => [11, 22],
                    'banner_sales_rules' => [33, 44],
                ],
            ],
        ];
    }
}
