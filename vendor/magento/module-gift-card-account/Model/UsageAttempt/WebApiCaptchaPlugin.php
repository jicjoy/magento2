<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\GiftCardAccount\Model\UsageAttempt;

use Magento\Captcha\Helper\Data as CaptchaHelper;
use Magento\GiftCardAccount\Model\Spi\UsageAttemptsManagerInterface;

/**
 * Forcing captcha mode as "fail after number of attempts" for web API requests
 * to prevent blocking gift card API completely.
 */
class WebApiCaptchaPlugin
{
    /**
     * @var UsageAttemptsManagerInterface
     */
    private $attemptManager;

    /**
     * @param UsageAttemptsManagerInterface $attemptManager
     */
    public function __construct(UsageAttemptsManagerInterface $attemptManager)
    {
        $this->attemptManager = $attemptManager;
    }

    /**
     * @param CaptchaHelper $subject
     * @param string $key
     * @param string $mode
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(CaptchaHelper $subject, $mode, $key)
    {
        if ($key === 'mode') {
            if ($this->attemptManager instanceof UsageAttemptsManager
                && $this->attemptManager->isForcingCaptchaMode()
            ) {
                $mode = CaptchaHelper::MODE_AFTER_FAIL;
            }
        }

        return $mode;
    }
}
