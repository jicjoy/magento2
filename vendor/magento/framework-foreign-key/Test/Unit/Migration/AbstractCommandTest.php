<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ForeignKey\Test\Unit\Migration;

use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResourceConnection\ConnectionFactory;
use Magento\Framework\Config\File\ConfigFilePool as Config;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\ForeignKey\Migration\AbstractCommand;
use Magento\Framework\ForeignKey\Migration\TableNameArrayIteratorFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var AbstractCommand
     */
    protected $command;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $writerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|InputInterface
     */
    protected $inputMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    protected $outputMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $host;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultConnection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $newConnection;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->readerMock = $this->createDefaultMock(Reader::class);
        $this->writerMock = $this->createDefaultMock(Writer::class);
        $this->factoryMock = $this->createDefaultMock(ConnectionFactory::class);

        $this->inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);

        $this->command = $this->getMockForAbstractClass(
            AbstractCommand::class,
            [],
            '',
            false,
            false,
            true,
            ['getCommandName', 'getCommandDescription', 'getCommandDefinition'],
            false
        );
        $this->command
            ->expects($this->once())
            ->method('getCommandName')
            ->willReturn('setup:db-schema:split');

        $this->command
            ->expects($this->once())
            ->method('getCommandDescription')
            ->willReturn('description');

        $this->command
            ->expects($this->once())
            ->method('getCommandDefinition')
            ->willReturn(
                $this->getDefinitions(['host', 'connection', 'resource', 'username', 'password', 'dbname'])
            );

        $this->defaultConnection = $this->createDefaultMock(Mysql::class);
        $this->newConnection = $this->createDefaultMock(Mysql::class);
    }

    /**
     * @param array $names
     *
     * @return array
     */
    protected function getDefinitions(array $names)
    {
        $definitions = [];

        foreach ($names as $name) {
            $definition = $this->createMock(InputOption::class);
            $definition->expects($this->atLeastOnce())->method('getName')->willReturn($name);
            $definitions[] = $definition;
        }

        return $definitions;
    }

    /**
     * @dataProvider executeProvider
     * @param array $config
     * @param array $newConnectionConfig
     * @param array $existingTables
     * @param array $tablesToMove
     * @throws \Exception
     */
    public function testExecute(array $config, array $newConnectionConfig, array $existingTables, array $tablesToMove)
    {
        $tableNamesToMove = array_keys($tablesToMove);
        $tableNameArrayFactoryMock = $this->getMockBuilder(TableNameArrayIteratorFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $tableNameArrayFactoryMock->expects($this->once())->method('create')->willReturn($tableNamesToMove);
        $arrayIteratorConnectionMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()->getMock();

        $arrayIteratorConnectionMock->expects($this->any())->method('getTableName')->willReturnArgument(0);
        $reflectedClass = new \ReflectionClass(AbstractCommand::class);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke(
            $this->command,
            $this->writerMock,
            $this->readerMock,
            $this->factoryMock,
            $tableNamesToMove,
            null,
            [],
            $tableNameArrayFactoryMock
        );
        $tablesToMoveWithForeignKeys = [];
        foreach ($tablesToMove as $tableName => $foreignKeys) {
            $tablesToMoveWithForeignKeys[$tableName] = $foreignKeys;
        }

        $optionMap = [
            ['host', 'test_host'],
            ['connection', 'new_connection'],
            ['dbname', 'new_db'],
            ['username', 'new_user_name'],
            ['password', 'new_user_password'],
            ['resource', 'new_resource'],
        ];
        $this->inputMock->expects($this->any())->method('getOption')->willReturnMap($optionMap);
        $this->readerMock->expects($this->once())->method('load')->with(Config::APP_ENV)->willReturn($config);
        $this->factoryMock->expects($this->at(0))
            ->method('create')
            ->with($config['db']['connection']['default'])
            ->willReturn($this->defaultConnection);

        $this->factoryMock->expects($this->at(1))
            ->method('create')
            ->with($newConnectionConfig)
            ->willReturn($this->newConnection);

        $updatedConfig = $this->getUpdatedConfig($config, $newConnectionConfig);

        $movedTables = array_intersect(array_keys($tablesToMoveWithForeignKeys), $existingTables);
        $remainingTables = array_diff($existingTables, array_keys($tablesToMoveWithForeignKeys));

        $this->writerMock->expects($this->once())->method('saveConfig')->with($updatedConfig, true);
        $this->outputMock->expects($this->once())->method('writeln')
            ->with('Migration has been finished successfully!');

        foreach ($tablesToMoveWithForeignKeys as $tableName => $foreignKeys) {
            $tableExists = in_array($tableName, $existingTables);
            $this->defaultConnection->method('isTableExists')
                ->with($tableName)
                ->willReturn($tableExists);
            if ($tableExists) {
                $data = ['id' => 10, 'name' => 'test'];
                $this->moveTableAsserts($tableName, $data);

                $this->newConnection->expects($this->any())
                    ->method('getForeignKeys')
                    ->with($tableName)
                    ->willReturn($foreignKeys);
            }
        }
        foreach ($remainingTables as $tableName) {
            $this->defaultConnection->expects($this->any())
                ->method('getForeignKeys')
                ->with($tableName)
                ->willReturn([]);
        }

        $this->newConnection->expects($this->any())
            ->method('getTables')
            ->willReturn($movedTables);
        $this->defaultConnection->expects($this->any())
            ->method('getTables')
            ->willReturn($remainingTables);

        $this->command->run($this->inputMock, $this->outputMock);
    }

    /**
     * @param string $tableName
     * @param array $data
     */
    protected function moveTableAsserts($tableName, array $data)
    {
        $select = $this->createMock(Select::class);
        $stmt = $this->createMock(\Zend_Db_Statement_Pdo::class);
        $this->defaultConnection->expects($this->once())->method('select')->willReturn($select);
        $select->expects($this->once())->method('from')->with($tableName)->willReturnSelf();
        $this->defaultConnection->expects($this->any())->method('query')->willReturn($stmt);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([$data]);
        $this->newConnection->expects($this->once())
            ->method('insertArray')
            ->with($tableName, array_keys($data), [$data]);
        $this->defaultConnection->expects($this->once())->method('dropTable')->with($tableName);
    }

    /**
     * @param array $config
     * @param array $newConfig
     *
     * @return array
     */
    protected function getUpdatedConfig(array $config, array $newConfig)
    {
        $updatedConfig = [Config::APP_ENV => $config];
        $updatedConfig[Config::APP_ENV]['db']['connection']['new_connection'] = $newConfig;
        $updatedConfig[Config::APP_ENV]['resource']['new_resource']['connection'] = 'new_connection';
        return $updatedConfig;
    }

    /**
     * @return array
     */
    public function executeProvider()
    {
        $config = [
            'db' => [
                'connection' => [
                    'default' => [
                        'host' => 'localhost',
                        'dbname' => 'test',
                        'username' => 'test',
                        'password' => '123123q',
                        'model' => 'mysql4',
                        'engine' => 'innodb',
                        'initStatements' => 'SET NAMES utf8;',
                        'active' => '1'
                    ]
                ],
                'table_prefix' => ''
            ]
        ];
        $newConnectionConfig = [
            'host' => 'test_host',
            'dbname' => 'new_db',
            'username' => 'new_user_name',
            'password' => 'new_user_password',
            'model' => 'mysql4',
            'engine' => 'innodb',
            'initStatements' => 'SET NAMES utf8;',
            'active' => '1'
        ];
        $foreignKeys = [
            [
                'REF_TABLE_NAME' => 'some_table',
                'TABLE_NAME' => 'test_table_1',
                'FK_NAME' => 'test_fk_name'
            ]
        ];

        return [
            [$config, $newConnectionConfig, [], ['test_table_1' => []]],
            [$config, $newConnectionConfig, ['test_table_1'], ['test_table_1' => []]],
            [$config, $newConnectionConfig, ['test_table_1', 'second'], ['test_table_1' => []]],
            [$config, $newConnectionConfig, ['test_table_1'], ['test_table_1' => $foreignKeys]]
        ];
    }

    /**
     * Creates default mock-object of class
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createDefaultMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
