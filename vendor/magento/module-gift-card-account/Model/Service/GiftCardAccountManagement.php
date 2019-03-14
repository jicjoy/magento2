<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Model\Service;

use Magento\Framework\App\ObjectManager;
use Magento\GiftCardAccount\Model\Giftcardaccount as GiftCardAccount;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptFactoryInterface;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptsManagerInterface;
use Magento\GiftCardAccount\Api\GiftCardAccountManagementInterface;

/**
 * Class GiftCardAccountManagement
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftCardAccountManagement implements GiftCardAccountManagementInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $giftCardHelper;

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCardAccountFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UsageAttemptsManagerInterface
     */
    private $attempts;

    /**
     * @var UsageAttemptFactoryInterface
     */
    private $attemptFactory;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardHelper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory giftcardaccountFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UsageAttemptsManagerInterface|null $attemptsManager
     * @param UsageAttemptFactoryInterface|null $attemptFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\GiftCardAccount\Helper\Data $giftCardHelper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardAccountFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UsageAttemptsManagerInterface $attemptsManager = null,
        UsageAttemptFactoryInterface $attemptFactory = null
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->giftCardHelper = $giftCardHelper;
        $this->giftCardAccountFactory = $giftCardAccountFactory;
        $this->storeManager = $storeManager;
        $this->attempts = $attemptsManager
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptsManagerInterface::class);
        $this->attemptFactory = $attemptFactory
            ?? ObjectManager::getInstance()
                ->get(UsageAttemptFactoryInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByQuoteId($cartId, $giftCardCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $this->attempts->attempt($this->attemptFactory->create($giftCardCode));
        /** @var \Magento\GiftCardAccount\Model\Giftcardaccount $giftCard */
        $giftCard = $this->giftCardAccountFactory->create();
        $giftCard->loadByCode($giftCardCode);

        try {
            $giftCard->removeFromCart(true, $quote);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete gift card from quote'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByQuoteId($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $giftCards = $this->giftCardHelper->getCards($quote);
        $cards = [];
        foreach ($giftCards as $giftCard) {
            $cards[] = $giftCard[GiftCardAccount::CODE];
        }
        $data = [
            GiftCardAccount::GIFT_CARDS => $cards,
            GiftCardAccount::GIFT_CARDS_AMOUNT => $quote->getGiftCardsAmount(),
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT => $quote->getBaseGiftCardsAmount(),
            GiftCardAccount::GIFT_CARDS_AMOUNT_USED => $quote->getGiftCardsAmountUsed(),
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT_USED => $quote->getBaseGiftCardsAmountUsed(),
        ];

        /** @var \Magento\GiftCardAccount\Model\Giftcardaccount $giftCardAccount */
        $giftCardAccount = $this->giftCardAccountFactory->create(['data' => $data]);
        return $giftCardAccount;
    }

    /**
     * {@inheritdoc}
     */
    public function saveByQuoteId(
        $cartId,
        \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
    ) {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $cardCode = $giftCardAccountData->getGiftCards();
        $cardCode = array_shift($cardCode);
        $this->attempts->attempt($this->attemptFactory->create($cardCode));
        /** @var \Magento\GiftCardAccount\Model\Giftcardaccount $giftCard */
        $giftCard = $this->giftCardAccountFactory->create();
        $giftCard->loadByCode($cardCode);
        try {
            $giftCard->addToCart(true, $quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not add gift card code'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function checkGiftCard($cartId, $giftCardCode)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $this->attempts->attempt($this->attemptFactory->create($giftCardCode));
        $giftCard = $this->giftCardAccountFactory->create();
        $giftCard->loadByCode($giftCardCode);
        try {
            $giftCard->isValid(true, true, true, false);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new CouldNotSaveException(__('Please correct the wrong or expired Gift Card Code.'));
        }
        /** @var \Magento\Directory\Model\Currency $currency */
        $currency = $this->storeManager->getStore()->getBaseCurrency();
        return $currency->convert($giftCard->getBalance(), $quote->getQuoteCurrencyCode());
    }
}
