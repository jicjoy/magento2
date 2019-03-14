<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Config;

use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactory;
use Magento\Framework\Config\ConfigOptionsListConstants;

class DbReader implements ReaderInterface
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactoryInterface
     */
    protected $connectionFactory;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @param ConnectionFactory $connectionFactory
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     */
    public function __construct(
        ConnectionFactory $connectionFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Load constraint configuration from all related databases
     *
     * @param string|null $scope
     * @return array
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function read($scope = null)
    {
        $connections = $this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS);
        $constraints = [];
        foreach ($connections as $connectionName => $connectionConfig) {
            $constraints = array_merge($constraints, $this->getDbConstraints($connectionName, $connectionConfig));
        }
        return $constraints;
    }

    /**
     * Retrieve constraints that are declared in given database
     *
     * @param string $connectionName
     * @param array $connectionConfig
     * @return array
     */
    private function getDbConstraints($connectionName, array $connectionConfig)
    {
        $connection = $this->connectionFactory->create($connectionConfig);
        $select = $connection->select()
            ->from(['info' => 'information_schema.KEY_COLUMN_USAGE'], [])
            ->joinInner(
                ['constraints' => 'information_schema.REFERENTIAL_CONSTRAINTS'],
                'constraints.CONSTRAINT_NAME = info.CONSTRAINT_NAME'
                . ' AND constraints.CONSTRAINT_SCHEMA = info.CONSTRAINT_SCHEMA',
                []
            )->where(
                'info.REFERENCED_TABLE_NAME IS NOT NULL'
            )->where(
                'info.CONSTRAINT_SCHEMA = ?',
                $connectionConfig['dbname']
            )->columns(
                [
                    'name' => 'constraints.CONSTRAINT_NAME',
                    'table_name' => 'info.TABLE_NAME',
                    'reference_table_name' => 'info.REFERENCED_TABLE_NAME',
                    'field_name' => 'info.COLUMN_NAME',
                    'reference_field_name' => 'info.REFERENCED_COLUMN_NAME',
                    'delete_strategy' => 'constraints.DELETE_RULE'
                ]
            );
        $rawData = $connection->fetchAssoc($select);
        $constraints = [];
        foreach ($rawData as $row) {
            $row['connection'] = $connectionName;
            $row['reference_connection'] = $connectionName;
            $row['delete_strategy'] = 'DB ' . $row['delete_strategy'];
            $key = $row['table_name']. $row['reference_table_name'] . $row['field_name'] . $row['reference_field_name'];
            $constraintId = sha1($key);
            $constraints[$constraintId] = $row;
        }
        return $constraints;
    }
}
