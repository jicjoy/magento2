<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Customerbalance helper
 *
 */
namespace Magento\CustomerBalance\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML configuration paths
     */
    const XML_PATH_ENABLED = 'customer/magento_customerbalance/is_enabled';

    const XML_PATH_AUTO_REFUND = 'customer/magento_customerbalance/refund_automatically';

    /**
     * Check whether customer balance functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1;
    }

    /**
     * Check if automatically refund is enabled
     *
     * @return bool
     */
    public function isAutoRefundEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_AUTO_REFUND, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
