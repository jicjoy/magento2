<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Solr\SearchAdapter;

/**
 * Interface \Magento\Solr\SearchAdapter\AccessPointMapperInterface
 *
 */
interface AccessPointMapperInterface
{
    /**
     * @return string
     */
    public function getHandler();
}
