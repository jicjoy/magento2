<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Preview;

use Magento\Store\Model\Store;
use Magento\Framework\App\Area;
use Magento\Staging\Model\VersionManager;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Route parameters preprocessor for staging preview.
 */
class RouteParamsPreprocessor implements \Magento\Framework\Url\RouteParamsPreprocessorInterface
{
    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param HttpRequest $request
     * @param VersionManager $versionManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        HttpRequest $request,
        VersionManager $versionManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->versionManager = $versionManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Adds 'version' and 'store' to query parameters.
     *
     * @param string $areaCode
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return array|null
     */
    public function execute($areaCode, $routePath, $routeParams)
    {
        if ($areaCode == Area::AREA_FRONTEND) {
            if ($this->versionManager->isPreviewVersion() && $routePath !== null) {
                $routeParams['_query'][VersionManager::PARAM_NAME]
                    = $this->versionManager->getRequestedTimestamp();

                if (!$this->isStoreCodeUsedInUrl()) {
                    $routeParams['_query'][StoreResolverInterface::PARAM_NAME]
                        = $this->request->getParam(StoreResolverInterface::PARAM_NAME);
                }
            }
        }

        return $routeParams;
    }

    /**
     * @return bool
     */
    private function isStoreCodeUsedInUrl()
    {
        return $this->scopeConfig->getValue(
            Store::XML_PATH_STORE_IN_URL,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
