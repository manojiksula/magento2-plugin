<?php

namespace Springbot\Main\Api\Entity;

/**
 * Interface AttributeSetRepositoryInterface
 * @package Springbot\Main\Api
 */
interface AttributeSetRepositoryInterface
{
    /**
     * Get store configuration
     *
     * @param int $storeId
     * @return \Springbot\Main\Api\Entity\Data\AttributeSetInterface[]
     */
    public function getList($storeId);

    /**
     * @param int $storeId
     * @param int $categoryId
     * @return \Springbot\Main\Api\Entity\Data\AttributeSetInterface
     */
    public function getFromId($storeId, $categoryId);
}
