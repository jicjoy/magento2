<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Test\Unit\Controller\Cart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RemoveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\GiftCardAccount\Controller\Cart\Remove
     */
    protected $removeController;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardAccountMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    protected function setUp()
    {
        $this->contextMock = $this->createMock(\Magento\Framework\App\Action\Context::class);
        $this->objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        $this->messageManagerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
        $this->resultRedirectFactory =
            $this->createMock(\Magento\Framework\Controller\Result\RedirectFactory::class);
        $this->redirectMock = $this->createMock(\Magento\Framework\App\Response\RedirectInterface::class);
        $this->requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $this->resultRedirectMock = $this->createMock(\Magento\Framework\Controller\Result\Redirect::class);
        $this->contextMock->expects($this->once())->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->contextMock->expects($this->once())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->once())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock
            ->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactory);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->giftCardAccountMock = $this->createMock(\Magento\GiftCardAccount\Model\Giftcardaccount::class);
        $this->escaperMock = $this->createMock(\Magento\Framework\Escaper::class);
        $this->removeController = new \Magento\GiftCardAccount\Controller\Cart\Remove(
            $this->contextMock,
            $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class),
            $this->createMock(\Magento\Checkout\Model\Session::class),
            $this->createMock(\Magento\Store\Model\StoreManagerInterface::class),
            $this->createMock(\Magento\Framework\Data\Form\FormKey\Validator::class),
            $this->createMock(\Magento\Checkout\Model\Cart::class)
        );
    }

    public function testExecute()
    {
        $valueMap = [
            ['code', null, 'gift_card_code'],
            ['return_url', null, false],
            ['in_cart', null, false]
        ];
        $this->requestMock->expects($this->any())->method('getParam')->willReturnMap($valueMap);
        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with(\Magento\GiftCardAccount\Model\Giftcardaccount::class)
            ->willReturn($this->giftCardAccountMock);
        $this->giftCardAccountMock
            ->expects($this->once())
            ->method('loadByCode')
            ->with('gift_card_code')
            ->willReturn($this->giftCardAccountMock);
        $this->giftCardAccountMock->expects($this->once())->method('removeFromCart');
        $this->objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->with(\Magento\Framework\Escaper::class)
            ->willReturn($this->escaperMock);
        $this->escaperMock->expects($this->once())->method('escapeHtml')->with('gift_card_code');
        $this->resultRedirectFactory->expects($this->once())->method('create')->willReturn($this->resultRedirectMock);
        $this->redirectMock->expects($this->once())->method('getRefererUrl')->willReturn('http://example.com');
        $this->resultRedirectMock
            ->expects($this->once())
            ->method('setUrl')
            ->with('http://example.com')
            ->willReturnSelf();
        $this->messageManagerMock->expects($this->once())->method('addSuccess');
        $this->assertEquals($this->resultRedirectMock, $this->removeController->execute());
    }
}
