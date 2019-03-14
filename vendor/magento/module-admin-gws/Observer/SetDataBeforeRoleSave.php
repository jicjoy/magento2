<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Exception\LocalizedException;

class SetDataBeforeRoleSave implements ObserverInterface
{
    /**
     * @var \Magento\AdminGws\Model\Role
     */
    protected $role;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->role = $role;
        $this->storeManager = $storeManager;
    }

    /**
     * Transform array of website ids and array of store group ids into comma-separated strings
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        $websiteIds = $object->getGwsWebsites();
        $storeGroupIds = $object->getGwsStoreGroups();

        // validate specified data
        if ($object->getGwsIsAll() === 0 && empty($websiteIds) && empty($storeGroupIds)) {
            throw new LocalizedException(__('Please specify at least one website or one store group.'));
        }
        if (!$this->role->getIsAll()) {
            if ($object->getGwsIsAll()) {
                throw new LocalizedException(__('You need more permissions to set All Scopes to a Role.'));
            }
        }

        if (empty($websiteIds)) {
            $websiteIds = [];
        } else {
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', $websiteIds);
            }
            $allWebsiteIds = array_keys($this->storeManager->getWebsites());
            foreach ($websiteIds as $websiteId) {
                if (!in_array($websiteId, $allWebsiteIds)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Incorrect website ID: %1', $websiteId)
                    );
                }
                // prevent granting disallowed websites
                if (!$this->role->getIsAll()) {
                    if (!$this->role->hasWebsiteAccess($websiteId, true)) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __(
                                'You need more permissions to access website "%1".',
                                $this->storeManager->getWebsite($websiteId)->getName()
                            )
                        );
                    }
                }
            }
        }
        if (empty($storeGroupIds)) {
            $storeGroupIds = [];
        } else {
            if (!is_array($storeGroupIds)) {
                $storeGroupIds = explode(',', $storeGroupIds);
            }
            $allStoreGroups = [];
            foreach ($this->storeManager->getWebsites() as $website) {
                $allStoreGroups = array_merge($allStoreGroups, $website->getGroupIds());
            }
            foreach ($storeGroupIds as $storeGroupId) {
                if (![$storeGroupId, $allStoreGroups]) {
                    throw new LocalizedException(__('Incorrect store ID: %1', $storeGroupId));
                }
                // prevent granting disallowed store group
                if (count(array_diff($storeGroupIds, $this->role->getStoreGroupIds()))) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('You need more permissions to save this setting.')
                    );
                }
            }
        }

        $object->setGwsWebsites(implode(',', $websiteIds));
        $object->setGwsStoreGroups(implode(',', $storeGroupIds));

        return $this;
    }
}
