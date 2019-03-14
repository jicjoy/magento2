<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\GiftCardAccount\Model\Spi\Exception;

/**
 * Too many attempts to use gift card codes were made.
 */
interface TooManyAttemptsExceptionInterface extends \Throwable
{

}
