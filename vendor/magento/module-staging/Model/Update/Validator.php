<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Staging\Model\Update;

use Magento\Framework\Exception\ValidatorException;
use Magento\Staging\Api\Data\UpdateInterface;

/**
 * Class Validator
 */
class Validator
{
    /**
     * @param UpdateInterface $entity
     * @return void
     * @throws ValidatorException
     */
    public function validateCreate(UpdateInterface $entity)
    {
        $this->validateUpdate($entity);
        $this->validateStartTimeNotPast($entity);
    }

    /**
     * @param UpdateInterface $entity
     * @return void
     * @throws ValidatorException
     */
    public function validateUpdate(UpdateInterface $entity)
    {
        if (!$entity->getName()) {
            throw new ValidatorException(__('Please select Name for Future Update.'));
        }

        if (!$entity->getStartTime()) {
            throw new ValidatorException(__('Please select Start Time for Future Update.'));
        }

        $this->validateEndTime($entity);
    }

    /**
     * @param UpdateInterface $entity
     * @return void
     * @throws ValidatorException
     */
    private function validateStartTimeNotPast(UpdateInterface $entity)
    {
        $currentDateTime = new \DateTime();
        if (strtotime($entity->getStartTime()) < $currentDateTime->getTimestamp()) {
            throw new ValidatorException(__('Future Update Start Time cannot be earlier than current time.'));
        }
    }

    /**
     * @param UpdateInterface $entity
     * @return void
     * @throws ValidatorException
     */
    private function validateEndTime(UpdateInterface $entity)
    {
        $currentDateTime = new \DateTime();

        $startTimeGreaterEndTime = strtotime($entity->getStartTime()) >= strtotime($entity->getEndTime());
        if ($entity->getEndTime() && $startTimeGreaterEndTime) {
            throw new ValidatorException(__('Future Update End Time cannot be equal or earlier than Start Time.'));
        }

        $endTimeLessCurrentTime = strtotime($entity->getEndTime()) <= $currentDateTime->getTimestamp();
        if ($entity->getEndTime() && $endTimeLessCurrentTime) {
            throw new ValidatorException(__('Future Update End Time cannot be earlier than current time.'));
        }
    }
}
