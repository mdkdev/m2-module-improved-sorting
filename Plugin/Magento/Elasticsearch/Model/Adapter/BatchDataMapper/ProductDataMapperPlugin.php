<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Plugin\Magento\Elasticsearch\Model\Adapter\BatchDataMapper;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;
use Mdkdev\ImprovedSorting\Model\ElasticSearch\Adapter\DataMapper\Attributes as AttributeDataMapper;

/**
 * Class ProductDataMapperPlugin
 * @package Mdkdev\ImprovedSorting\Plugin\Magento\Elasticsearch\Model\Adapter\BatchDataMapper
 */
class ProductDataMapperPlugin
{
    /**
     * @param AttributeDataMapper $attributeDataMapper
     */
    public function __construct(private readonly AttributeDataMapper $attributeDataMapper)
    {}

    /**
     * @param ProductDataMapper $subject
     * @param $documents
     * @param $documentData
     * @param $storeId
     * @param $context
     * @return mixed
     */
    public function afterMap(
        ProductDataMapper $subject,
        $documents,
        $documentData,
        $storeId,
        $context
    ): mixed {
        foreach ($documents as $productId => $document) {
            $document = \array_merge($document, $this->attributeDataMapper->map($productId));
            $documents[$productId] = $document;
        }

        return $documents;
    }
}
