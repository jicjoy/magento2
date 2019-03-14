<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model;

/**
 * Interface \Magento\Solr\Model\AdapterFactoryInterface
 *
 */
interface AdapterFactoryInterface
{
    /**
     * Return search adapter
     *
     * @return \Magento\Solr\Model\Adapter\Solarium
     */
    public function createAdapter();
}
