<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Source\Category;

use Magento\Framework\DataObject;

/**
 * Lightweight data model class for items of \Magento\Catalog\Model\ResourceModel\Category\Collection
 *
 * @since 1.15.2
 */
class DataModel extends DataObject
{
    public function getId(): int
    {
        return (int)$this->getDataByKey('entity_id');
    }

    public function getLevel(): int
    {
        return (int)$this->getDataByKey('level');
    }

    public function getName(): string
    {
        return (string)$this->getDataByKey('name');
    }

    public function getPosition(): int
    {
        return (int)$this->getDataByKey('position');
    }

    public function getParentId(): int
    {
        return (int)$this->getDataByKey('parent_id');
    }

    public function getChildrenCount(): int
    {
        return (int)$this->getDataByKey('children_count');
    }
}
