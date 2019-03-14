<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\GiftCardAccount\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GiftcardaccountTest extends TestCase
{
    /**
     * @var Giftcardaccount
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->get(Giftcardaccount::class);
    }

    /**
     * @magentoDataFixture Magento/GiftCardAccount/_files/giftcardaccount.php
     */
    public function testLoadByCode()
    {
        $code = 'giftcardaccount_fixture';
        $card = $this->model->loadByCode($code);
        $this->assertEquals($code, $card->getCode());
        $this->assertEquals(
            9.99,
            $card->getBalance()
        );
    }
}
