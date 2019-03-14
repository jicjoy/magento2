<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Observer;

use Magento\Framework\Event\ObserverInterface;

class EnterpriseCustomerAttributeBeforeSave implements ObserverInterface
{
    /**
     * Before save observer for customer attribute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            /**
             * Check for maximum attribute_code length
             */
            $attributeCodeMaxLength = \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9;
            $validate = \Zend_Validate::is(
                $attribute->getAttributeCode(),
                'StringLength',
                ['max' => $attributeCodeMaxLength]
            );
            if (!$validate) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('An attribute code must not be more than %1 characters.', $attributeCodeMaxLength)
                );
            }
        }

        return $this;
    }
}
