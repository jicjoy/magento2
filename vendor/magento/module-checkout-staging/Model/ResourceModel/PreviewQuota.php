<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CheckoutStaging\Model\ResourceModel;

use Magento\CheckoutStaging\Setup\InstallSchema;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PreviewQuota extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(InstallSchema::PREVIEW_QUOTA_TABLE, InstallSchema::ID_FIELD_NAME);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function insert($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(InstallSchema::PREVIEW_QUOTA_TABLE)
            ->where('quote_id = ?', (int) $id);
        if (!empty($connection->fetchRow($select))) {
            return true;
        }
        return 1 === $connection->insert(
            $this->getTable(InstallSchema::PREVIEW_QUOTA_TABLE),
            ['quote_id' => (int) $id]
        );
    }
}
