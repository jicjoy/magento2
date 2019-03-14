<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSearch\Model\Client;

use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @api
 * @since 100.1.0
 */
class ClientResolver
{
    /**
     * Scope configuration
     *
     * @var ScopeConfigInterface
     * @since 100.1.0
     */
    protected $scopeConfig;

    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     * @since 100.1.0
     */
    protected $objectManager;

    /**
     * Pool of existing client factories
     *
     * @var array
     */
    private $clientFactoryPool;

    /**
     * Pool of client option classes
     *
     * @var array
     */
    private $clientOptionsPool;

    /**
     * Config path
     *
     * @var string
     * @since 100.1.0
     */
    protected $path;

    /**
     * Config Scope
     * @since 100.1.0
     */
    protected $scope;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ScopeConfigInterface $scopeConfig,
     * @param array $clientFactories
     * @param array $clientOptions
     * @param string $path
     * @param string $scopeType
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        array $clientFactories,
        array $clientOptions,
        $path,
        $scopeType
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->clientFactoryPool = $clientFactories;
        $this->clientOptionsPool = $clientOptions;
        $this->path = $path;
        $this->scope = $scopeType;
    }

    /**
     * Returns configured search engine
     *
     * @return string
     * @since 100.1.0
     */
    public function getCurrentEngine()
    {
        return $this->scopeConfig->getValue($this->path, $this->scope);
    }

    /**
     * Create client instance
     *
     * @param string $engine
     * @param array $data
     * @return ClientInterface
     * @since 100.1.0
     */
    public function create($engine = '', array $data = [])
    {
        $engine = $engine ?: $this->getCurrentEngine();

        if (!isset($this->clientFactoryPool[$engine])) {
            throw new \LogicException(
                'There is no such client factory: ' . $engine
            );
        }
        $factoryClass = $this->clientFactoryPool[$engine];
        $factory = $this->objectManager->create($factoryClass);
        if (!($factory instanceof ClientFactoryInterface)) {
            throw new \InvalidArgumentException(
                'Client factory must implement \Magento\AdvancedSearch\Model\Client\ClientFactoryInterface'
            );
        }

        $optionsClass = $this->clientOptionsPool[$engine];
        $clientOptions = $this->objectManager->create($optionsClass);
        if (!($clientOptions instanceof ClientOptionsInterface)) {
            throw new \InvalidArgumentException(
                'Client options must implement \Magento\AdvancedSearch\Model\Client\ClientInterface'
            );
        }

        $client = $factory->create($clientOptions->prepareClientOptions($data));

        return $client;
    }
}
