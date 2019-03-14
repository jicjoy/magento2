<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\SearchAdapter;

/**
 * Response Factory
 */
class ResponseFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Document Factory
     *
     * @var DocumentFactory
     */
    protected $documentFactory;

    /**
     * Aggregation Factory
     *
     * @var AggregationFactory
     */
    protected $aggregationFactory;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory
    ) {
        $this->objectManager = $objectManager;
        $this->documentFactory = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
    }

    /**
     * Create Query Response instance
     *
     * @param  array $response
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function create($response)
    {
        $documents = [];
        foreach ($response['documents'] as $rawDocument) {
            /** @var \Magento\Framework\Api\Search\Document[] $documents */
            $documents[] = $this->documentFactory->create(
                $rawDocument
            );
        }
        /** @var \Magento\Framework\Search\Response\Aggregation $aggregations */
        $aggregations = $this->aggregationFactory->create($response['aggregations']);
        return $this->objectManager->create(
            \Magento\Framework\Search\Response\QueryResponse::class,
            [
                'documents' => $documents,
                'aggregations' => $aggregations
            ]
        );
    }
}
