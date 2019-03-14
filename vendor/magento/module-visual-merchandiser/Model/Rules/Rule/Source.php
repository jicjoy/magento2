<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\Rules\Rule;

class Source extends \Magento\VisualMerchandiser\Model\Rules\Rule
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function applyToCollection($collection)
    {
        $options = $this->toMappedOptions(
            $this->_attribute->getSource()->getAllOptions(false, true)
        );

        $selectedOption = strtolower($this->_rule['value']);
        if (!isset($options[$selectedOption])) {
            $sourceId = $selectedOption;
        } else {
            $sourceId = $options[$selectedOption];
        }

        $collection->addAttributeToFilter($this->_rule['attribute'], [
            $this->_rule['operator'] => $sourceId
        ]);
    }
}
