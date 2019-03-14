<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Customer Attribute form.
 */
class CustomerCustomAttributesForm extends FormTabs
{
    /**
     * Get fields status array.
     *
     * @param array $fields [optional]
     * @return array
     */
    public function getFieldsStatus(array $fields = [])
    {
        $tab = $this->getTab('properties');
        $dataMapping = $tab->dataMapping($fields);
        $fieldsStatuses = [];
        foreach ($dataMapping as $code => $field) {
            $fieldsStatuses[$code] = $this->_rootElement
                ->find($field['selector'], $field['strategy'])
                ->isDisabled();
        }

        return $fieldsStatuses;
    }
}
