<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

use Magento\Framework\Exception\LocalizedException;

class ConfigureWishlistItem extends ConfigureOrderedItem
{
    /**
     * Create item
     *
     * @param string $itemId
     * @return \Magento\Wishlist\Model\Item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createItem($itemId)
    {
        if (!$itemId) {
            throw new LocalizedException(__('The wish list item id is not received.'));
        }

        $item = $this->_objectManager->create(
            \Magento\Wishlist\Model\Item::class
        )->loadWithOptions(
            $itemId,
            'info_buyRequest'
        );
        if (!$item->getId()) {
            throw new LocalizedException(__('The wish list item is not loaded.'));
        }
        return $item;
    }
}
