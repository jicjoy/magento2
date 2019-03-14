<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Block\Adminhtml\Rma\Edit\Item\Form\Element;

/**
 * Test class for Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Boolean
 */
class BooleanTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Boolean
     */
    protected $booleanItem;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->booleanItem = $objectManager->getObject(
            \Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Boolean::class
        );
    }

    public function testConstruct()
    {
        $expectedValues = [['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]];
        $this->assertEquals($expectedValues, $this->booleanItem->getValues());
    }
}
