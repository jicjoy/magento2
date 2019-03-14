<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount;

class Index extends \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount
{
    /**
     * Defines if status message of code pool is show
     *
     * @var bool
     */
    protected $_showCodePoolStatusMessage = true;

    /**
     * Default action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_showCodePoolStatusMessage) {
            $usage = $this->_objectManager->create(\Magento\GiftCardAccount\Model\Pool::class)->getPoolUsageInfo();

            $url = $this->_objectManager->get(
                \Magento\Backend\Model\UrlInterface::class
            )->getUrl('adminhtml/*/generate');
            $notice = __(
                'Code Pool used: <b>%1%</b> (free <b>%2</b> of <b>%3</b> total). Generate new code pool <a href="%4">here</a>.',
                round($usage->getPercent(), 2),
                $usage->getFree(),
                $usage->getTotal(),
                $url
            );
            if ($usage->getPercent() == 100) {
                $this->messageManager->addError($notice);
            } else {
                $this->messageManager->addNotice($notice);
            }
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_GiftCardAccount::customer_giftcardaccount');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Gift Card Accounts'));
        $this->_view->renderLayout();
    }

    /**
     * Setter for code pool status message flag
     *
     * @param bool $isShow
     * @return void
     */
    public function setShowCodePoolStatusMessage($isShow)
    {
        $this->_showCodePoolStatusMessage = (bool)$isShow;
    }
}
