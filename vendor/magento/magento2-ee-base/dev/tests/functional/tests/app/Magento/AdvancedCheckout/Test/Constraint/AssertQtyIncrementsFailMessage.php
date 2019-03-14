<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertQtyIncrementsFailMessage
 * Assert that requested qty does not meet specified increments error message is displayed after adding products to cart
 */
class AssertQtyIncrementsFailMessage extends AbstractConstraint
{
    /**
     * Qty increments error message
     */
    const QTY_INCREMENTS_ERROR_MESSAGE = 'You can buy this product only in quantities of %d at a time.';

    /**
     * Assert that requested qty does not meet the increments error message is displayed after adding products to cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            $currentMessage = $checkoutCart->getAdvancedCheckoutCart()->getFailedItemErrorMessage($product);
            \PHPUnit_Framework_Assert::assertEquals(
                sprintf(self::QTY_INCREMENTS_ERROR_MESSAGE, $product->getData('stock_data')['qty_increments']),
                $currentMessage,
                'Wrong error message is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Requested qty does not meet the increments error message is present after adding products to cart.';
    }
}
