<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdminGws\Test\Unit\Block\Adminhtml\Plugin\Catalog\Product\Edit\Button;

use Magento\AdminGws\Block\Adminhtml\Plugin\Catalog\Product\Edit\Button\AddAttribute;
use Magento\AdminGws\Model\Role;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test for Magento\AdminGws\Block\Adminhtml\Plugin\Catalog\Product\Edit\Button
 */
class AddAttributeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Mock result
     */
    const MOCK_RESULT = ['result'];

    /**
     * @var Role|MockObject
     */
    private $roleMock;

    /**
     * @var AddAttribute
     */
    private $plugin;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->roleMock = $this->getMockBuilder(Role::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->plugin = $objectManager->getObject(AddAttribute::class, ['role' => $this->roleMock]);
    }

    /**
     * @param bool $isAll
     * @param array $expected
     * @return void
     * @dataProvider afterGetButtonDataDataProvider
     */
    public function testAfterGetButtonData(bool $isAll, array $expected)
    {
        $this->roleMock->expects($this->once())
            ->method('getIsAll')
            ->willReturn($isAll);
        /** @var \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\AddAttribute|MockObject $addAttributeMock */
        $addAttributeMock = $this->getMockBuilder(
            \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\AddAttribute::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertEquals($expected, $this->plugin->afterGetButtonData($addAttributeMock, self::MOCK_RESULT));
    }

    /**
     * @return array
     */
    public function afterGetButtonDataDataProvider() : array
    {
        return [
            'isAll' => [true, self::MOCK_RESULT],
            '!isAll' => [false, []],
        ];
    }
}
