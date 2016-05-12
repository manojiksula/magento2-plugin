<?php

namespace Springbot\Main\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Springbot\Main\Helper\Data as SpringbotHelper;

/**
 * Class Async
 *
 * @package Springbot\Main\Block
 */
class Async extends Template
{

    protected $springbotHelper;
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     * @param SpringbotHelper $springbotHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        SpringbotHelper $springbotHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->springbotHelper = $springbotHelper;
        parent::__construct($context);
    }

    /**
     * Default domain that serves our JS script
     *
     * @return string
     */
    public function getAssetsDomain()
    {
        return $this->scopeConfig->getValue('springbot/configuration/assets_domain');
    }

    /**
     * Get the GUID for the async code
     *
     * @return string
     */
    public function getStoreGuid()
    {
        return strtolower($this->springbotHelper->getStoreGuid());
    }
}
