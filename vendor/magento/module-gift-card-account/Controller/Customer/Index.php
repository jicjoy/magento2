<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\GiftCardAccount\Model\Spi\Exception\TooManyAttemptsExceptionInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptFactoryInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptsManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends \Magento\Framework\App\Action\Action
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
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @inheritDoc
     * @param UsageAttemptsManagerInterface|null $attemptManager
     * @param UsageAttemptFactoryInterface|null $attemptFactory
     * @param FormKeyValidator|null $formKeyValidator
     */
    public function __construct(
        Context $context,
        UsageAttemptsManagerInterface $attemptManager = null,
        UsageAttemptFactoryInterface $attemptFactory = null,
        FormKeyValidator $formKeyValidator = null
    ) {
        parent::__construct($context);
        $this->attempts = $attemptManager
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptsManagerInterface::class);
        $this->attemptFactory = $attemptFactory
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptFactoryInterface::class);
        $this->formKeyValidator = $formKeyValidator
            ?? ObjectManager::getInstance()->get(FormKeyValidator::class);
    }

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get(\Magento\Customer\Model\Session::class)->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Redeem gift card
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (isset($data['giftcard_code'])) {
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                $this->messageManager->addErrorMessage(
                    __('Invalid Form Key. Please refresh the page.')
                );
                $this->_redirect('*/*/*');
                return;
            }

            $code = $data['giftcard_code'];
            try {
                if (!$this->_objectManager->get(\Magento\CustomerBalance\Helper\Data::class)->isEnabled()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('You can\'t redeem a gift card now.'));
                }
                $this->attempts->attempt($this->attemptFactory->create($code));
                $this->_objectManager->create(
                    \Magento\GiftCardAccount\Model\Giftcardaccount::class
                )->loadByCode(
                    $code
                )->setIsRedeemed(
                    true
                )->redeem();
                $this->messageManager->addSuccess(
                    __(
                        'Gift Card "%1" was redeemed.',
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($code)
                    )
                );
            } catch (TooManyAttemptsExceptionInterface $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot redeem this gift card.'));
            }
            $this->_redirect('*/*/*');
            return;
        }
        $this->_view->loadLayout();
        $this->_view->loadLayoutUpdates();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Gift Card'));
        $this->_view->renderLayout();
    }
}
