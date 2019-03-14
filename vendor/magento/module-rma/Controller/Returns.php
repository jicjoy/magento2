<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Rma\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

abstract class Returns extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Validator|null $formKeyValidator
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Validator $formKeyValidator = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->create(Validator::class);

        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get(\Magento\Customer\Model\Url::class)->getLoginUrl();

        if (!$this->_objectManager->get(\Magento\Customer\Model\Session::class)->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Check order view availability
     *
     * @param \Magento\Rma\Model\Rma|\Magento\Sales\Model\Order $item
     * @return bool
     */
    protected function _canViewOrder($item)
    {
        $customerId = $this->_objectManager->get(\Magento\Customer\Model\Session::class)->getCustomerId();
        if ($item->getId() && $customerId && $item->getCustomerId() == $customerId) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidRma($entityId = null)
    {
        $entityId = $entityId ?: (int) $this->getRequest()->getParam('entity_id');
        if (!$entityId || !$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
            return false;
        }

        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = $this->_objectManager->create(\Magento\Rma\Model\Rma::class)->load($entityId);
        if ($this->_canViewOrder($rma)) {
            $this->_coreRegistry->register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Checks whether RMA module is enabled in system config
     *
     * @return boolean
     */
    protected function _isEnabledOnFront()
    {
        /** @var $rmaHelper \Magento\Rma\Helper\Data */
        $rmaHelper = $this->_objectManager->get(\Magento\Rma\Helper\Data::class);
        return $rmaHelper->isEnabled();
    }
}
