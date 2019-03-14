<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\ResourceModel\Rule;

use Magento\TargetRule\Model\ResourceModel\Rule\Collection as RuleCollection;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RuleCollection
     */
    private $collection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            RuleCollection::class
        );
    }

    /**
     * @magentoDataFixture Magento/TargetRule/_files/upsell_rule_with_customer_segment.php
     * @magentoDbIsolation enabled
     */
    public function testCollectionContainsCustomerSegmentData()
    {
        $this->collection->addIsActiveFilter()->setPriorityOrder()->setFlag('do_not_run_after_load', true);

        foreach ($this->collection as $rule) {
            $this->assertNotEmpty($rule->getCustomerSegmentIds(), 'Empty customer segment data');
        }
    }
}
