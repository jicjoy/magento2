<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\GiftCardAccount\Model\UsageAttempt;

use Magento\Framework\Exception\LocalizedException;
use Magento\GiftCardAccount\Model\Spi\Exception\TooManyAttemptsExceptionInterface;

class TooManyAttemptsException extends LocalizedException implements TooManyAttemptsExceptionInterface
{

}
