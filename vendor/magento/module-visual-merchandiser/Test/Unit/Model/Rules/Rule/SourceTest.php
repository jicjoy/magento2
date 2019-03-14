<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Model\Rules\Rule;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\VisualMerchandiser\Model\Rules\Rule\Source as RuleSource;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class SourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var RuleSource
     */
    private $model;

    /**
     * @var ProductCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productCollectionMock;

    /**
     * @var Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeMock;

    /**
     * @var AbstractSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractSourceMock;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->productCollectionMock = $this->getMockBuilder(ProductCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['addAttributeToFilter'])
            ->getMock();

        $this->attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSource'])
            ->getMock();

        $this->abstractSourceMock = $this->getMockForAbstractClass(AbstractSource::class);
    }

    /**
     * @param array $options
     * @param string $value
     * @param string $attribute
     * @param array $condition
     * @dataProvider getAllOptionsDataProvider
     */
    public function testApplyToCollection(array $options, $value, $attribute, array $condition)
    {
        $this->model = $this->objectManagerHelper->getObject(
            RuleSource::class,
            [
                '_attribute' => $this->attributeMock,
                '_rule' => [
                    'attribute' => 'color',
                    'operator' => 'eq',
                    'value' => $value
                ]
            ]
        );

        $this->attributeMock->expects($this->once())
            ->method('getSource')
            ->willReturn($this->abstractSourceMock);

        $this->abstractSourceMock->expects($this->once())
            ->method('getAllOptions')
            ->with(false, true)
            ->willReturn($options);

        $this->productCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with($attribute, $condition)
            ->willReturnSelf();

        $this->model->applyToCollection($this->productCollectionMock);
    }

    /**
     * @return array
     */
    public function getAllOptionsDataProvider()
    {
        return [
            [
                [
                    [
                        'value' => '16',
                        'label' => 'blck'
                    ],
                    [
                        'value' => '17',
                        'label' => 'wht'
                    ]
                ],
                'blck',
                'color',
                ['eq' => '16']
            ],
            [
                [
                    [
                        'value' => '16',
                        'label' => 'blck'
                    ],
                    [
                        'value' => '17',
                        'label' => 'wht'
                    ]
                ],
                'black',
                'color',
                ['eq' => 'black']
            ]
        ];
    }
}
