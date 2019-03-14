<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Model\Banner;

class Validator
{
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var array
     */
    protected $preparePostKeys =[
        'banner_catalog_rules',
        'banner_sales_rules'
    ];

    /**
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        $this->storeManager = $storeManager;
        $this->jsHelper = $jsHelper;
    }

    /**
     * Prepare data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareSaveData(array $data)
    {
        $data = $this->filterDisallowedData($data);
        $data = $this->preparePostData($data);
        return $data;
    }

    /**
     * Filter disallowed data
     *
     * @param array $data
     * @return array
     */
    protected function filterDisallowedData(array $data)
    {
        $currentStores = array_keys($this->storeManager->getStores(true));

        if (isset($data['store_contents_not_use'])) {
            $data['store_contents_not_use'] = array_intersect($data['store_contents_not_use'], $currentStores);
        }

        if (isset($data['store_contents'])) {
            $data['store_contents'] = array_intersect_key($data['store_contents'], array_flip($currentStores));
        }

        return $data;
    }

    /**
     * Prepare post data
     *
     * @param array $data
     * @return array
     */
    protected function preparePostData(array $data)
    {
        foreach ($this->preparePostKeys as $postKey) {
            if (isset($data[$postKey])) {
                $related = $this->jsHelper->decodeGridSerializedInput($data[$postKey]);

                foreach ($related as $key => $rid) {
                    $related[$key] = (int)$rid;
                }
                $data[$postKey] = $related;
            }
        }
        return $data;
    }
}
