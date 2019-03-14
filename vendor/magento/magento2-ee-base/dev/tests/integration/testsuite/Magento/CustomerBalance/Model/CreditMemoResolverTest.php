<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerBalance\Model;

use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class CreditMemoResolverTest extends TestCase
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $this->order = $objectManager->create(
            Order::class,
            [
                'data' => [
                    'state' => Order::STATE_PROCESSING,
                    'total_paid' => 20,
                    'base_total_paid' => 20,
                    'total_refunded' => 0
                ]
            ]
        );
    }

    /**
     * Checks if Credit Memo can be created depends on different totals.
     *
     * @param float $totalInvoiced
     * @param float $balanceInvoiced
     * @param float $rewardInvoiced
     * @param float $giftCardInvoiced
     * @param float $totalRefunded
     * @param float $balanceRefunded
     * @param float $rewardRefunded
     * @param float $giftCardRefunded
     * @param bool $expected
     * @dataProvider totalsDataProvider
     */
    public function testIsCreditMemoAvailable(
        float $totalInvoiced,
        float $balanceInvoiced,
        float $rewardInvoiced,
        float $giftCardInvoiced,
        float $totalRefunded,
        float $balanceRefunded,
        float $rewardRefunded,
        float $giftCardRefunded,
        bool $expected
    ) {
        $this->order->setBaseTotalInvoiced($totalInvoiced);
        $this->order->setBaseCustomerBalanceInvoiced($balanceInvoiced);
        $this->order->setBaseRwrdCrrncyAmtInvoiced($rewardInvoiced);
        $this->order->setBaseGiftCardsInvoiced($giftCardInvoiced);

        $this->order->setBaseTotalRefunded($totalRefunded);
        $this->order->setBaseCustomerBalanceRefunded($balanceRefunded);
        $this->order->setBaseRwrdCrrncyAmntRefnded($rewardRefunded);
        $this->order->setBaseGiftCardsRefunded($giftCardRefunded);

        self::assertEquals($expected, $this->order->canCreditmemo());
    }

    /**
     * Gets list of totals variations.
     *
     * @return array
     */
    public function totalsDataProvider(): array
    {
        return [
            [
                'totalInvoiced' => 10,
                'balanceInvoiced' => 0,
                'rewardInvoiced' => 0,
                'giftCardInvoiced' => 0,
                'totalRefunded' => 0,
                'balanceRefunded' => 0,
                'rewardRefunded' => 0,
                'giftCardRefunded' => 0,
                'expected' => true
            ],
            [
                'totalInvoiced' => 20,
                'balanceInvoiced' => 0,
                'rewardInvoiced' => 0,
                'giftCardInvoiced' => 0,
                'totalRefunded' => 10,
                'balanceRefunded' => 3,
                'rewardRefunded' => 6,
                'giftCardRefunded' => 0,
                'expected' => true
            ],
            [
                'totalInvoiced' => 0,
                'balanceInvoiced' => 0,
                'rewardInvoiced' => 0,
                'giftCardInvoiced' => 20,
                'totalRefunded' => 10,
                'balanceRefunded' => 3,
                'rewardRefunded' => 6,
                'giftCardRefunded' => 0,
                'expected' => true
            ],
            [
                'totalInvoiced' => 15,
                'balanceInvoiced' => 5,
                'rewardInvoiced' => 0,
                'giftCardInvoiced' => 0,
                'totalRefunded' => 15,
                'balanceRefunded' => 5,
                'rewardRefunded' => 0,
                'giftCardRefunded' => 0,
                'expected' => false
            ],
            [
                'totalInvoiced' => 0,
                'balanceInvoiced' => 5,
                'rewardInvoiced' => 5,
                'giftCardInvoiced' => 10,
                'totalRefunded' => 0,
                'balanceRefunded' => 15,
                'rewardRefunded' => 5,
                'giftCardRefunded' => 0,
                'expected' => false
            ]
        ];
    }
}
