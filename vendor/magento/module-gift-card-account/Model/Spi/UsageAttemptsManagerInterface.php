<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\GiftCardAccount\Model\Spi;

use Magento\GiftCardAccount\Model\Spi\Data\UsageAttemptInterface;
use Magento\GiftCardAccount\Model\Spi\Exception\TooManyAttemptsExceptionInterface;

/**
 * Log and manage attempts to use gift card codes.
 */
interface UsageAttemptsManagerInterface
{
    /**
     * Attempt to use a gift card code.
     *
     * @param UsageAttemptInterface $attempt
     * @throws TooManyAttemptsExceptionInterface
     *
     * @return void
     */
    public function attempt(UsageAttemptInterface $attempt);
}
