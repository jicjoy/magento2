<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\ResourceModel;

class IndexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TargetRule\Model\ResourceModel\Index
     */
    private $model;

    protected function setUp()
    {
        $this->model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\TargetRule\Model\ResourceModel\Index::class
        );
    }

    /**
     * @return array
     */
    public function getOperatorConditionDataProvider()
    {
        return [
            ['category_id', '==', 1, '`category_id`=1'],
            ['category_id', '>=', 1, '`category_id`>=1'],
            ['category_id', '()', [2, 4], '`category_id` IN(2, 4)'],
            ['category_id', '!()', [2, 4], '`category_id` NOT IN(2, 4)'],
            ['category_id', '{}', 8, '`category_id` LIKE \'%8%\''],
            ['category_id', '!{}', 8, '`category_id` NOT LIKE \'%8%\''],
        ];
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @param string $expected
     *
     * @dataProvider getOperatorConditionDataProvider
     */
    public function testGetOperatorCondition($field, $operator, $value, $expected)
    {
        $result = $this->model->getOperatorCondition($field, $operator, $value);

        $this->assertEquals($expected, $result);
    }
}
