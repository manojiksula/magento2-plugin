<?php

namespace Springbot\Main\Api\Entity\Data;

/**
 * Interface AttributeSetInterface
 * @package Springbot\Main\Api\Entity\Data
 */
interface AttributeSetInterface extends \Magento\Eav\Api\Data\AttributeSetInterface
{

    /**
     * @return \Magento\Eav\Model\Entity\Attribute[]
     */
    public function getAttributes();
}
