<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Api\Counter;

/**
 * Interface ItemInterface
 * @api
 * @since 100.0.2
 */
interface ItemInterface
{
    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * @return int
     */
    public function getQty();
}
