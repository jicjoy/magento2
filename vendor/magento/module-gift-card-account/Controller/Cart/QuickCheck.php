<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Controller\Cart;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\GiftCardAccount\Model\Spi\Exception\TooManyAttemptsExceptionInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptFactoryInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptsManagerInterface;

class QuickCheck extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var UsageAttemptsManagerInterface
     */
    private $attempts;

    /**
     * @var UsageAttemptFactoryInterface
     */
    private $attemptFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param UsageAttemptsManagerInterface|null $attemptManager
     * @param UsageAttemptFactoryInterface|null $attemptFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        UsageAttemptsManagerInterface $attemptManager = null,
        UsageAttemptFactoryInterface $attemptFactory = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->attempts = $attemptManager
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptsManagerInterface::class);
        $this->attemptFactory = $attemptFactory
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptFactoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Check a gift card account availability
     * @throws NotFoundException
     * @return void
     */
    public function execute()
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $this->_coreRegistry->unregister('current_giftcardaccount_check_error');
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundException(__('Invalid Request'));
        } else {
            try {
                $code = $request->getParam('giftcard_code', '');
                $this->attempts->attempt($this->attemptFactory->create($code));
                /** @var \Magento\GiftCardAccount\Model\Giftcardaccount $card */
                $card = $this->_objectManager->create(
                    \Magento\GiftCardAccount\Model\Giftcardaccount::class
                );
                $card = $card->loadByCode($code);
                $this->_coreRegistry->register('current_giftcardaccount', $card);
                try {
                    $card->isValid(true, true, true, false);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $card->unsetData();
                }
            } catch (TooManyAttemptsExceptionInterface $exception) {
                $this->_coreRegistry->register(
                    'current_giftcardaccount_check_error',
                    $exception->getMessage()
                );
            }
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
