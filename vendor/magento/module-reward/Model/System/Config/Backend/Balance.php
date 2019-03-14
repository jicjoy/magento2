<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Backend model for "Reward Points Balance"
 *
 */
namespace Magento\Reward\Model\System\Config\Backend;

use Magento\Framework\Exception\LocalizedException;

class Balance extends \Magento\Framework\App\Config\Value
{
    /**
     * Check if max_points_balance >= than min_points_balance
     * (max allowed to RP to gain is more than minimum to redeem)
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        if ($this->getFieldsetDataValue('min_points_balance') < 0) {
            $message = __('"Minimum Reward Points Balance" should be either a positive number or left empty.');
            throw new LocalizedException($message);
        }
        if ($this->getFieldsetDataValue('max_points_balance') < 0) {
            $message = __('"Cap Reward Points Balance" should be either a positive number or left empty.');
            throw new LocalizedException($message);
        }
        if ($this->getFieldsetDataValue(
            'max_points_balance'
        ) && $this->getFieldsetDataValue(
            'min_points_balance'
        ) > $this->getFieldsetDataValue(
            'max_points_balance'
        )
        ) {
            $message = __('"Minimum Reward Points Balance" should be less or equal to "Cap Reward Points Balance".');
            throw new LocalizedException($message);
        }
        return $this;
    }
}
