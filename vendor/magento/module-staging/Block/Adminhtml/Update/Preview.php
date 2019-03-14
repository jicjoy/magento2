<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Block\Adminhtml\Update;

/**
 * Staging preview block.
 *
 * @api
 * @since 100.1.0
 */
class Preview extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Staging\Model\VersionManager
     * @since 100.1.0
     */
    protected $versionManager;

    /**
     * @var \Magento\Framework\Url
     * @since 100.1.0
     */
    protected $frontendUrl;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     * @since 100.1.0
     */
    protected $sessionManager;

    /**
     * @var \Magento\Framework\Session\SidResolverInterface
     * @since 100.1.0
     */
    protected $sidResolver;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Staging\Model\VersionManager $versionManager
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Staging\Model\VersionManager $versionManager,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        array $data = []
    ) {
        $this->frontendUrl = $frontendUrl;
        $this->versionManager = $versionManager;
        $this->sessionManager = $sessionManager;
        $this->sidResolver = $context->getSidResolver();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Form preparation
     *
     * @return void
     * @since 100.1.0
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldSet = $form->addFieldset(
            'calendar_fieldset',
            [
                'legend' => __('View Specific Date'),
                'collapsable' => false,
                'class' => 'view-specific-date'
            ]
        );

        $fieldSet->addField(
            'staging_date',
            'date',
            [
                'label' => __('Date & Time'),
                'title' => __('Date & Time'),
                'name' => 'staging_date',
                'date_format' => $this->getDateFormat(),
                'time_format' => $this->getTimeFormat(),
                'value' => $this->getRequestedDateTime()
            ]
        );
        $this->setForm($form);
    }

    /**
     * Get logo picture url
     *
     * @return string
     * @since 100.1.0
     */
    public function getLogoSrc()
    {
        return $this->getViewFileUrl('images/magento-icon.svg');
    }

    /**
     * Get url for logo
     *
     * @return mixed
     * @since 100.1.0
     */
    public function getHomeUrl()
    {
        return $this->_urlBuilder->getUrl('staging/update/index');
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getPreviewBackendUrl()
    {
        return $this->_urlBuilder->getUrl('staging/update/preview');
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getPreviewFrontendUrl()
    {
        $previewUrl = urldecode(
            $this->getRequest()->getParam($this->getPreviewUrlParamName())
        );

        $sidParamName = $this->getSidParamName();
        $sidParamValue = $this->sessionManager->getSessionId();

        $versionParamName = $this->getVersionParamName();
        $versionParamValue = $this->getPreviewVersion();

        $storeParamName = $this->getStoreParamName();
        $storeParamValue = $this->getPreviewStoreCode();

        if ($previewUrl) {
            $ampersand = strpos($previewUrl, '?') === false ? '?' : '&';

            if (strpos($previewUrl, $sidParamName) === false) {
                $params[] = $sidParamName . '=' . $sidParamValue;
            }

            if (strpos($previewUrl, $versionParamName) === false) {
                $params[] = $versionParamName . '=' . $versionParamValue;
            }

            if (!$this->isStoreCodeUsedInUrl()) {
                $params[] = $storeParamName . '=' . $storeParamValue;
            }

            if (!empty($params)) {
                $previewUrl .= $ampersand . implode('&', $params);
            }
        } else {
            $previewUrl = $this->frontendUrl->getUrl(
                null,
                [
                    '_query' => [
                        $sidParamName => $sidParamValue,
                        $versionParamName => $versionParamValue
                    ]
                ]
            );
        }

        return $this->modifyHost($previewUrl);
    }

    /**
     * Gets caption for 'Calendar' tab.
     *
     * @return string
     * @since 100.1.0
     */
    public function getCalendarTabCaption()
    {
        $date = $this->getRequestedDateTime();
        $format = $this->getDateTimeFormat();

        return $this->_localeDate->formatDateTime($date, null, null, null, null, $format);
    }

    /**
     * Gets code of current store or returns default.
     *
     * @return string
     * @since 100.1.0
     */
    public function getPreviewStoreCode()
    {
        $code = $this->getRequest()->getParam($this->getPreviewStoreParamName());

        if (!$code) {
            $code = $this->_storeManager->getDefaultStoreView()->getCode();
        }

        return $code;
    }

    /**
     * Gets version of the preview.
     *
     * @return string
     * @since 100.1.0
     */
    public function getPreviewVersion()
    {
        return $this->getRequest()->getParam($this->getPreviewVersionParamName());
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getSidParamName()
    {
        return $this->sidResolver->getSessionIdQueryParam(
            $this->sessionManager
        );
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getVersionParamName()
    {
        return \Magento\Staging\Model\VersionManager::PARAM_NAME;
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getStoreParamName()
    {
        return \Magento\Store\Api\StoreResolverInterface::PARAM_NAME;
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getPreviewUrlParamName()
    {
        return \Magento\Staging\Model\Preview\UrlBuilder::PARAM_PREVIEW_URL;
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getPreviewStoreParamName()
    {
        return \Magento\Staging\Model\Preview\UrlBuilder::PARAM_PREVIEW_STORE;
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getPreviewVersionParamName()
    {
        return \Magento\Staging\Model\Preview\UrlBuilder::PARAM_PREVIEW_VERSION;
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getStoreSelectorOptions()
    {
        $data = [];

        /** @var $website \Magento\Store\Model\Website */
        foreach ($this->_storeManager->getWebsites() as $website) {
            $websiteItem = [
                'label' => $this->escapeHtml($website->getName())
            ];

            /** @var $group \Magento\Store\Model\Group */
            foreach ($website->getGroups() as $group) {
                $groupItem = [
                    'label' => $this->escapeHtml($group->getName())
                ];

                /** @var $store \Magento\Store\Model\Store */
                foreach ($group->getStores() as $store) {
                    if ($store->isActive()) {
                        $storeItem = [
                            'label' => $this->escapeHtml($store->getName())
                        ];

                        $storeItem['baseUrl'] = $this->modifyHost($store->getBaseUrl());
                        $storeItem['value'] = $this->escapeHtml($store->getCode());

                        $groupItem['value'][] = $storeItem;
                    }
                }

                $websiteItem['value'][] = $groupItem;
            }

            $data[] = $websiteItem;
        }

        return json_encode($data);
    }

    /**
     * @return int
     * @since 100.1.0
     */
    public function getTimezoneOffset()
    {
        return $this->getRequestedDateTime()->getOffset();
    }

    /**
     * @return string
     * @since 100.1.0
     */
    public function getDateTimeFormat()
    {
        return $this->getDateFormat() . ' ' . $this->getTimeFormat();
    }

    /**
     * @return bool
     */
    private function isStoreCodeUsedInUrl()
    {
        return $this->_scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_STORE_IN_URL
        );
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function modifyHost($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        if ($port) {
            $host .= ':' . $port;
        }
        return $url = str_replace(
            $host,
            $this->_request->getServer('HTTP_HOST'),
            $url
        );
    }

    /**
     * Gets requested date and time from requested preview version.
     *
     * @return \DateTime
     */
    private function getRequestedDateTime()
    {
        $requestedDateTime = new \DateTime();

        if ($this->getPreviewVersion()) {
            $requestedDateTime->setTimestamp($this->getPreviewVersion());
        }

        return $this->_localeDate->date($requestedDateTime);
    }

    /**
     * @return string
     */
    private function getDateFormat()
    {
        return $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
    }

    /**
     * @return string
     */
    private function getTimeFormat()
    {
        return $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::SHORT
        );
    }
}
