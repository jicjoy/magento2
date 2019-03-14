<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\GiftCardAccount\Model\UsageAttempt;

use Magento\GiftCardAccount\Model\Spi\Data\UsageAttemptInterface;

class UsageAttempt implements UsageAttemptInterface
{
    /**
     * @var int|null
     */
    private $customerId;

    /**
     * @var string
     */
    private $code;

    /**
     * @param int|null $customerId
     * @param string $code
     */
    public function __construct(
        int $customerId = null,
        string $code
    ) {
        $this->customerId = $customerId;
        $this->code = $code;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
