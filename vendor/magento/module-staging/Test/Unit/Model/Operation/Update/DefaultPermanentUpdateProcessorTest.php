<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Operation\Update;

use Magento\Framework\EntityManager\TypeResolver;
use Magento\Staging\Model\Entity\VersionLoader;
use Magento\Staging\Model\Operation\Update\DefaultPermanentUpdateProcessor;
use Magento\Staging\Model\ResourceModel\Db\ReadEntityVersion;
use Magento\Staging\Model\VersionManager;
use Magento\Staging\Model\Operation\Delete\UpdateIntersectedRollbacks;
use Magento\Framework\EntityManager\MetadataPool;

class DefaultPermanentUpdateProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DefaultPermanentUpdateProcessor
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ReadEntityVersion
     */
    private $entityVersionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UpdateIntersectedRollbacks
     */
    private $updateIntersectedUpdatesMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MetadataPool
     */
    private $metadataPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $hydratorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $metaDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|VersionManager
     */
    private $versionManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|VersionLoader
     */
    private $versionLoaderMock;

    /**
     * @var string
     */
    private $entityType;

    protected function setUp()
    {
        $this->entityType = 'EntityType';
        $this->metaDataMock = $this->createMock(\Magento\Framework\EntityManager\EntityMetadataInterface::class);
        $this->hydratorMock = $this->createMock(\Magento\Framework\EntityManager\HydratorInterface::class);
        $this->objectMock = $this->createMock(\Magento\Framework\Model\AbstractExtensibleModel::class);
        $this->entityVersionMock = $this->createMock(ReadEntityVersion::class);
        $this->updateIntersectedUpdatesMock = $this->createMock(UpdateIntersectedRollbacks::class);
        $this->metadataPoolMock = $this->createMock(MetadataPool::class);
        /** @var \PHPUnit_Framework_MockObject_MockObject|TypeResolver $typeResolverMock */
        $typeResolverMock = $this->createMock(TypeResolver::class);
        $typeResolverMock->expects($this->any())
            ->method('resolve')
            ->willReturn($this->entityType);
        $this->versionManagerMock = $this->createMock(VersionManager::class);
        $this->versionLoaderMock = $this->createMock(VersionLoader::class);
        $this->model = new DefaultPermanentUpdateProcessor(
            $typeResolverMock,
            $this->entityVersionMock,
            $this->updateIntersectedUpdatesMock,
            $this->metadataPoolMock,
            $this->versionManagerMock,
            $this->versionLoaderMock
        );
    }

    public function testProcess()
    {
        $versionId = 1;
        $rollbackId = null;
        $entityData = [
            'id' => 1
        ];
        $nextVersionId = 2;
        $nextPermanentVersionId = 3;
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getHydrator')
            ->with($this->entityType)
            ->willReturn($this->hydratorMock);
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with($this->entityType)
            ->willReturn($this->metaDataMock);
        $this->hydratorMock
            ->expects($this->once())
            ->method('extract')
            ->with($this->objectMock)
            ->willReturn($entityData);
        $this->metaDataMock->expects($this->once())->method('getIdentifierField')->willReturn('id');
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextVersionId);
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextPermanentVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextPermanentVersionId);
        $this->updateIntersectedUpdatesMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->objectMock, $nextPermanentVersionId);
        $this->model->process($this->objectMock, $versionId, $rollbackId);
    }

    public function testProcessPermanentUpdate()
    {
        $versionId = 1;
        $rollbackId = null;
        $entityData = [
            'id' => 1
        ];
        $nextVersionId = 2;
        $nextPermanentVersionId = 2;
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getHydrator')
            ->with($this->entityType)
            ->willReturn($this->hydratorMock);
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with($this->entityType)
            ->willReturn($this->metaDataMock);
        $this->hydratorMock
            ->expects($this->once())
            ->method('extract')
            ->with($this->objectMock)
            ->willReturn($entityData);
        $this->metaDataMock->expects($this->once())->method('getIdentifierField')->willReturn('id');
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextVersionId);
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextPermanentVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextPermanentVersionId);
        $this->updateIntersectedUpdatesMock
            ->expects($this->never())
            ->method('execute');
        $this->model->process($this->objectMock, $versionId, $rollbackId);
    }

    public function testProcessPermanentMadeTemporaryUpdate()
    {
        $firstVersionId = 1;
        $versionId = 2;
        $rollbackId = 5;
        $entityData = [
            'id' => 1
        ];
        $nextVersionId = 3;
        $nextPermanentVersionId = 4;
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getHydrator')
            ->with($this->entityType)
            ->willReturn($this->hydratorMock);
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with($this->entityType)
            ->willReturn($this->metaDataMock);
        $this->hydratorMock
            ->expects($this->once())
            ->method('extract')
            ->with($this->objectMock)
            ->willReturn($entityData);
        $this->metaDataMock->expects($this->once())->method('getIdentifierField')->willReturn('id');
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextVersionId')
            ->with($this->entityType, $versionId, $entityData['id'])
            ->willReturn($nextVersionId);
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextPermanentVersionId')
            ->with($this->entityType, $versionId, $entityData['id'])
            ->willReturn($nextPermanentVersionId);
        $this->updateIntersectedUpdatesMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->objectMock, $nextPermanentVersionId);
        $this->versionManagerMock
            ->expects($this->once())
            ->method('setCurrentVersionId')
            ->withConsecutive([$versionId]);
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getPreviousPermanentVersionId')
            ->with($this->entityType, $versionId, $entityData['id'])
            ->willReturn($firstVersionId);
        $this->versionLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->objectMock, $entityData['id'], $firstVersionId)
            ->willReturn($this->objectMock);
        $this->model->process($this->objectMock, $versionId, $rollbackId);
    }
}
