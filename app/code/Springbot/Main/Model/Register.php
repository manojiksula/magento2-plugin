<?php

namespace Springbot\Main\Model;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Springbot\Main\Helper\Data;

/**
 * Class Register
 * @package Springbot\Main\Model
 */
class Register extends AbstractModel
{
    const API_CLASS = 'stores';

    /**
     * @var Data
     */
    private $_helper;

    /**
     * @var Config
     */
    private $_config;

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfigInterface;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param Data $data
     */
    public function __construct(
        Config $config,
        Context $context,
        Data $data,
        Registry $registry,
        ScopeConfigInterface $scopeConfigInterface,
        StoreManagerInterface $storeManager
    )
    {
        $this->_config = $config;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_helper = $data;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry);
    }

    /**
     * Register all stores with the Springbot app
     *
     * @return void
     */
    public function registerStores()
    {
        $guid = $this->_helper->getStoreGuid();
        $response = $this->query($guid, $this->getStoreArray($guid));

        if ($response['status'] == 'ok' && isset($response['stores']))
        {
            $springbotStoreId = array_search($guid, $response['stores']);
            $vars = [
                'store_guid' => $guid,
                'store_id' => $springbotStoreId,
                'security_token' => $this->_scopeConfigInterface->getValue('springbot/configuration/security_token')
            ];

            $this->commitVars($vars);
            $this->_cacheManager->clean();
        }
    }

    /**
     * @param $guid
     */
    public function getStoreArray($guid)
    {
        $storeUrl = $this->_scopeConfigInterface->getValue('web/unsecure/base_url');
        $store = $this->_storeManager->getStore();

        $storeDetail = [
            'guid' => $guid,
            'url' => $storeUrl,
            'name' => $store->getName(),
            'logo_src' => $this->_scopeConfigInterface->getValue('design/header/logo_src'),
            'logo_alt_tag' => $this->_scopeConfigInterface->getValue('design/header/logo_atl'),
            'json_data' => [
                'web_id' => $store->getWebsiteId(),
                'store_id' => $this->getId(),
                'store_name' => $store->getName(),
                'store_code' => $store->getCode(),
                'store_active' => $store->getIsActive(),
                'store_url' => $storeUrl,
                'media_url' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
                'store_mail_address' => $this->getStoreAddress(),
                'store_custsrv_email' => $this->_scopeConfigInterface('trans_email/ident_support/email'),
                'store_statuses' => $this->getStoreStatuses($this->getId())
            ]
        ];

        return $storeDetail;
    }

    protected function commitVars($vars)
    {
        foreach ($vars as $key => $value)
        {
            $configKey = $this->makeConfigKey($key, $this->_storeManager->getStore()->getId());
            $this->_config->saveConfig($configKey, $value, 'default', 0);
        }
    }

    protected function makeConfigKey($dataClass, $storeId = '')
    {
        $configKey = 'springbot/configuration/' . $dataClass;
        if ($storeId != '') {
            $configKey = $configKey . '_' . $storeId;
        }

        return $configKey;
    }

    protected function query($guid, $storeMetaData)
    {
        return $this->_helper->apiPostWrapped(self::API_CLASS, [$guid => $storeMetaData]);
    }
}