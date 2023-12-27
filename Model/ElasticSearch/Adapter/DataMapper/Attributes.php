<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\ElasticSearch\Adapter\DataMapper;

use Mdkdev\ImprovedSorting\Model\Attributes as EavAttributes;
use Mdkdev\ImprovedSorting\Model\ResourceModel\ImprovedSorting as ResourceModel;

/**
 * Class Attributes
 * @package Mdkdev\ImprovedSorting\Model\ElasticSearch\Adapter\DataMapper
 */
class Attributes
{
    /**
     * @param EavAttributes $eavAttributes
     * @param ResourceModel $resourceModel
     */
    public function __construct(
        private readonly EavAttributes $eavAttributes,
        private readonly ResourceModel $resourceModel
    ) {}

    /**
     * @param $entityId
     * @return array
     */
    public function map($entityId): array
    {
        $entityId = (int)$entityId;
        $attributeValues = [];

        foreach ($this->eavAttributes->getAttributes() as $attribute) {
            $attributeValues[$attribute] = $this->resourceModel->getAttributeValue(
                $entityId,
                $attribute
            );
        }

        return $attributeValues;
    }
}
