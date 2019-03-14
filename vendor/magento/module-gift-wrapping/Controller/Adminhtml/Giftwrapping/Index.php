<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

/**
 * @codeCoverageIgnore
 */
class Index extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * List of gift wrappings
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->initResultPage();
        return $resultPage;
    }
}
