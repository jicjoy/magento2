<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Api\Data;

/**
 * Gift Card Account data
 *
 * @codeCoverageIgnore
 * @api
 * @since 100.0.2
 */
interface GiftCardAccountInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Gift cards codes
     *
     * @return string[]
     */
    public function getGiftCards();

    /**
     * Set Gift cards codes
     *
     * @param array $cards
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setGiftCards(array $cards);

    /**
     * Gift cards amount in quote currency
     *
     * @return float
     */
    public function getGiftCardsAmount();

    /**
     * Set Gift cards amount in quote currency
     *
     * @param float $amount
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setGiftCardsAmount($amount);

    /**
     * Gift cards amount in base currency
     *
     * @return float
     */
    public function getBaseGiftCardsAmount();

    /**
     * Set Gift cards amount in base currency
     *
     * @param float $amount
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setBaseGiftCardsAmount($amount);

    /**
     * Gift cards amount used in quote currency
     *
     * @return float
     */
    public function getGiftCardsAmountUsed();

    /**
     * Set Gift cards amount used in quote currency
     *
     * @param float $amount
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setGiftCardsAmountUsed($amount);

    /**
     * Gift cards amount used in base currency
     *
     * @return float
     */
    public function getBaseGiftCardsAmountUsed();

    /**
     * Set Gift cards amount used in base currency
     *
     * @param float $amount
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setBaseGiftCardsAmountUsed($amount);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\GiftCardAccount\Api\Data\GiftCardAccountExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\GiftCardAccount\Api\Data\GiftCardAccountExtensionInterface $extensionAttributes
    );
}
