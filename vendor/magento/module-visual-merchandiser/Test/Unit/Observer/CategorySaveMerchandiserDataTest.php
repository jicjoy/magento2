<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Observer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\VisualMerchandiser\Model\Position\Cache;
use Magento\VisualMerchandiser\Model\Rules;
use Magento\VisualMerchandiser\Observer\CategorySaveMerchandiserData;

/**
 * Test for \Magento\VisualMerchandiser\Observer\CategorySaveMerchandiserData
 */
class CategorySaveMerchandiserDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CategorySaveMerchandiserData
     */
    private $categorySaveMerchandiserDataObserver;

    /**
     * @var Cache
     */
    private $cacheMock;

    /**
     * @var Rules
     */
    private $rulesMock;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepositoryMock;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $requestMock;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->setMethods(['getPostValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->cacheKey = '5a8fb8ef75270';
        $this->cacheMock = $this->getMockBuilder(Cache::class)
            ->setMethods(['getPositions'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->rulesMock = $this->getMockBuilder(Rules::class)
            ->setMethods(['loadByCategory'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->categorySaveMerchandiserDataObserver = $objectManagerHelper->getObject(
            CategorySaveMerchandiserData::class,
            [
                '_cache' => $this->cacheMock,
                '_rules' => $this->rulesMock,
                'categoryRepository' => $this->categoryRepositoryMock,
            ]
        );
    }

    /**
     * Test for new category.
     *
     * @return void
     */
    public function testNewCategoryExecute()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->with(\Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY)
            ->willReturn($this->cacheKey);
        $this->cacheMock->expects($this->once())->method('getPositions')->willReturn(false);
        $categoryMock = $this->getCategoryMock([], null, true);
        $eventMock = $this->getEventMock($categoryMock);
        $observerMock = $this->getObserverMock($eventMock);

        $this->categorySaveMerchandiserDataObserver->execute($observerMock);
    }

    /**
     * Test for category with matching products by rule.
     *
     * @dataProvider smartCategoryDataProvider
     * @param array $postData
     * @param string $methodsCall
     * @return void
     */
    public function testSmartCategoryExecute(array $postData, $methodsCall)
    {
        $origData = [
            'entity_id' => $postData['entity_id'],
            'name' => 'TEST',
        ];
        $this->requestMock->expects($this->exactly(2))
            ->method('getPostValue')
            ->willReturnMap(
                [
                    [\Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY, null, $this->cacheKey],
                    [null, null, $postData],
                ]
            );
        $this->cacheMock->expects($this->once())->method('getPositions')->willReturn(false);

        $categoryMock = $this->getCategoryMock($origData, $postData['entity_id'], false);
        $eventMock = $this->getEventMock($categoryMock);
        $observerMock = $this->getObserverMock($eventMock);

        $ruleMock = $this->getMockBuilder(\Magento\VisualMerchandiser\Model\Rules::class)
            ->setMethods(['setData', 'save', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $ruleMock->expects($this->$methodsCall())->method('getId')->willReturn(null);
        $ruleMock->expects($this->$methodsCall())->method('setData');
        $ruleMock->expects($this->$methodsCall())->method('save');

        $this->rulesMock->expects($this->$methodsCall())
            ->method('loadByCategory')
            ->with($categoryMock)
            ->willReturn($ruleMock);

        $this->categorySaveMerchandiserDataObserver->execute($observerMock);
    }

    /**
     * @return array
     */
    public function smartCategoryDataProvider()
    {
        $entityId = 3;

        return [
            [
                [
                    \Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY => $this->cacheKey,
                    'entity_id' => $entityId,
                    'is_smart_category' => true,
                    'smart_category_rules' => '[{"attribute":"price","operator":"lt","value":"100","logic":"OR"}]',
                ],
                'once',
            ],
            [
                [
                    \Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY => $this->cacheKey,
                    'entity_id' => $entityId,
                ],
                'never',
            ],
        ];
    }

    /**
     * @param array $origData
     * @param int|null $categoryId
     * @param bool $isObjectNew
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCategoryMock(array $origData, $categoryId, $isObjectNew)
    {
        $categoryMock = $this->getMockBuilder(\Magento\Catalog\Model\Category::class)
            ->setMethods(['isObjectNew', 'getId', 'getOrigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $categoryMock->expects($this->any())
            ->method('isObjectNew')
            ->willReturn($isObjectNew);

        $categoryMock->expects($this->any())
            ->method('getId')
            ->willReturn($categoryId);

        $categoryMock->expects($this->any())
            ->method('getOrigData')
            ->willReturn($origData);

        return $categoryMock;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $categoryMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEventMock(\PHPUnit_Framework_MockObject_MockObject $categoryMock)
    {
        $eventMock = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->setMethods(['getCategory', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getCategory')
            ->willReturn($categoryMock);
        $eventMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        return $eventMock;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $eventMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getObserverMock(\PHPUnit_Framework_MockObject_MockObject $eventMock)
    {
        $observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();

        $observerMock->expects($this->any())->method('getEvent')->willReturn($eventMock);

        return $observerMock;
    }
}
