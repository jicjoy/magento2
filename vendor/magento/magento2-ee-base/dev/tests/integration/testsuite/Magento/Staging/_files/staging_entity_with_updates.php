<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Staging\Api\UpdateRepositoryInterface;
use Magento\Staging\Api\Data\UpdateInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var \Magento\Catalog\Model\ResourceModel\Product $resourceModel */
$resourceModel = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Product::class);
/** @var $resource Magento\Framework\App\ResourceConnection */
$entityIdField = $resourceModel->getIdFieldName();
$entityTable = $resourceModel->getTable('catalog_product_entity');
$sequenceTable = $resourceModel->getTable('sequence_product');
$connection = $resourceModel->getConnection();

$endTime = strtotime('+50 minutes');
$updates = [
    [
        'name' => 'Update 1',
        'start_time' => date('Y-m-d H:i:s', strtotime('+40 minutes')),
        'end_time' => date('Y-m-d H:i:s', $endTime),
        'rollback_id' => $endTime
    ],
    [
        'name' => 'Update 1',
        'start_time' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        'end_time' => date('Y-m-d H:i:s', $endTime),
        'rollback_id' => $endTime
    ],
];

$rollBack = [
    'name' => 'Rollback for "Update 1"',
    'start_time' => date('Y-m-d H:i:s', $endTime),
    'is_rollback' => 1
];

/** @var UpdateRepositoryInterface $updateRepository */
$updateRepository = $objectManager->get(UpdateRepositoryInterface::class);
/** @var UpdateInterface $entity */
$entity = $objectManager->create(UpdateInterface::class, ['data' => $rollBack]);
$updateRepository->save($entity);

$connection->query(
    "INSERT INTO {$sequenceTable} (`sequence_value`) VALUES (1);"
);
$rowIdNum = 1;
$previousCreatedIn = 1;
foreach ($updates as $update) {
    /** @var UpdateInterface $entity */
    $entity = $objectManager->create(UpdateInterface::class, ['data' => $update]);
    $updateRepository->save($entity);

    $entityUpdate = [
        'row_id' => $rowIdNum++,
        $entityIdField => 1,
        'created_in' => $previousCreatedIn,
        'updated_in' => $entity->getId(),
        'attribute_set_id' => 1,
        'type_id' => 'simple',
        'sku' => 'productSku',
        'has_options' => 0,
        'required_options' => 0
    ];
    $connection->query(
        "INSERT INTO {$entityTable} (`row_id`, `{$entityIdField}`, `created_in`, `updated_in`, `attribute_set_id`,"
        . "`type_id`, `sku`, `has_options`, `required_options`)"
        . " VALUES (:row_id, :{$entityIdField}, :created_in, :updated_in, :attribute_set_id, :type_id, :sku,"
        . " :has_options, :required_options);",
        $entityUpdate
    );

    $previousCreatedIn = $entity->getId();
}
