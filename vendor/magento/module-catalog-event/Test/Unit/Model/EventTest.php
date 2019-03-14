<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Test\Unit\Model;

use Magento\CatalogEvent\Model\Event;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Category;

class EventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\CatalogEvent\Model\Event
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(Event::class);
    }

    protected function tearDown()
    {
        $this->model = null;
    }

    public function testGetIdentities()
    {
        $categoryId = 'categoryId';
        $eventId = 'eventId';
        $this->model->setCategoryId($categoryId);
        $this->model->setId($eventId);
        $eventTags = [
            Event::CACHE_TAG . '_' . $eventId,
            Category::CACHE_TAG . '_' . $categoryId,
            Event::CACHE_EVENT_CATEGORY_TAG . '_' . $categoryId,
        ];
        $this->assertEquals($eventTags, $this->model->getIdentities());
    }
}
