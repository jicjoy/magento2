<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Plugin\Store\Controller\Store;

use Magento\Store\Model\StoreResolver;
use Magento\Framework\Exception\LocalizedException;

/**
 * Plugin for store switch action.
 */
class SwitchAction
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Staging\Model\VersionManager
     */
    private $versionManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Staging\Model\VersionManager $versionManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Staging\Model\VersionManager $versionManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ) {
        $this->request = $request;
        $this->versionManager = $versionManager;
        $this->redirect = $redirect;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param \Magento\Store\Controller\Store\SwitchAction $subject
     * @param \Closure $proceed
     *
     * @return void
     */
    public function aroundExecute(
        \Magento\Store\Controller\Store\SwitchAction $subject,
        \Closure $proceed
    ) {
        if ($this->versionManager->isPreviewVersion()) {
            $this->updateRequestParams();

            try {
                $store = $this->storeRepository->getActiveStoreByCode(
                    $this->request->getParam(StoreResolver::PARAM_NAME)
                );

                $subject->getResponse()->setRedirect(
                    $this->prepareRedirectUrl($store->getCode())
                );
            } catch (LocalizedException $e) {
                $proceed();
            }
        } else {
            $proceed();
        }
    }

    /**
     * @return void
     */
    private function updateRequestParams()
    {
        $requestParams = $this->request->getParams();

        $requestParams[StoreResolver::PARAM_NAME]
            = $this->request->getPost(StoreResolver::PARAM_NAME);

        $this->request->setParams($requestParams);
    }

    /**
     * @param string $storeCode
     *
     * @return string
     */
    private function prepareRedirectUrl($storeCode)
    {
        $redirectUrlParts = parse_url(
            $this->redirect->getRedirectUrl()
        );

        parse_str($redirectUrlParts['query'], $redirectUrlQuery);

        $redirectUrlQuery[StoreResolver::PARAM_NAME] = $storeCode;

        $redirectUrlParts['query'] = http_build_query($redirectUrlQuery);

        return $this->buildUrl($redirectUrlParts);
    }

    /**
     * Builds an URL from parts returned by parse_url function.
     *
     * @param array $parts
     *
     * @return string
     */
    private function buildUrl(array $parts)
    {
        $url = $parts['scheme'] . '://' . $parts['host'];

        $url .= (isset($parts['port']) ? ':' . $parts['port'] : '');
        $url .= (isset($parts['path']) ? $parts['path'] : '');
        $url .= (isset($parts['query']) ? '?' . $parts['query'] : '');

        return $url;
    }
}
