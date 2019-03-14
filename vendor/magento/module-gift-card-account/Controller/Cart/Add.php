<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Controller\Cart;

use Magento\Framework\App\ObjectManager;
use Magento\GiftCardAccount\Model\Spi\Exception\TooManyAttemptsExceptionInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptFactoryInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptsManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var UsageAttemptsManagerInterface
     */
    private $attempts;

    /**
     * @var UsageAttemptFactoryInterface
     */
    private $attemptFactory;

    /**
     * @inheritDoc
     * @param UsageAttemptsManagerInterface|null $attemptManager
     * @param UsageAttemptFactoryInterface|null $attemptFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        UsageAttemptsManagerInterface $attemptManager = null,
        UsageAttemptFactoryInterface $attemptFactory = null
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->attempts = $attemptManager
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptsManagerInterface::class);
        $this->attemptFactory = $attemptFactory
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptFactoryInterface::class);
    }

    /**
     * Add Gift Card to current quote
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(
                __('Invalid Form Key. Please refresh the page.')
            );
        } elseif (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                $this->attempts->attempt($this->attemptFactory->create($code));
                if (strlen($code) > \Magento\GiftCardAccount\Helper\Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please correct the gift card code.'));
                }
                $this->_objectManager->create(
                    \Magento\GiftCardAccount\Model\Giftcardaccount::class
                )->loadByCode(
                    $code
                )->addToCart();
                $this->messageManager->addSuccess(
                    __(
                        'Gift Card "%1" was added.',
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($code)
                    )
                );
            } catch (TooManyAttemptsExceptionInterface $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot apply this gift card.'));
            }
        }

        return $this->_goBack();
    }
}
