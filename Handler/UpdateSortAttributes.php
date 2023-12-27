<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Handler;

use Exception;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\Store\Model\StoreManagerInterface;
use Mdkdev\ImprovedSorting\Model\Cache\Category\Purge as CategoryCachePurger;
use Mdkdev\ImprovedSorting\Model\Client\ElasticSearch as ElasticSearchClient;
use Mdkdev\ImprovedSorting\Model\Collector\CollectorInterface;
use Mdkdev\ImprovedSorting\Model\Collector\CollectorPool;
use Mdkdev\ImprovedSorting\Model\ResourceModel\ImprovedSorting as ResourceModel;

/**
 * Class UpdateSortAttributes
 * @package Mdkdev\ImprovedSorting\Handler
 */
class UpdateSortAttributes
{
    /**
     * @param CategoryCachePurger $categoryCachePurger
     * @param CollectorPool $collectorPool
     * @param ResourceModel $improvedSortingResourceModel
     * @param ElasticSearchClient $elasticSearchClient
     * @param ProductCategoryList $productCategoryList
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly CategoryCachePurger $categoryCachePurger,
        private readonly CollectorPool $collectorPool,
        private readonly ResourceModel $improvedSortingResourceModel,
        private readonly ElasticSearchClient $elasticSearchClient,
        private readonly ProductCategoryList $productCategoryList,
        private readonly StoreManagerInterface $storeManager
    ) {}

    /**
     * @return $this
     * @throws Exception
     */
    public function update(): self
    {
        if (!$this->elasticSearchClient->getClient()) {
            return $this;
        }

        $categoryIds = [];

        if ($sortingData = $this->getSortingData()) {
            $this->improvedSortingResourceModel->refreshSortingTable($sortingData);

            foreach ($this->getStoreIds() as $storeId) {
                foreach ($sortingData as $productId => $productData) {
                    unset($productData[CollectorInterface::PRODUCT_ID]);

                    $hasChanges = false;
                    $eSData = $this->elasticSearchClient->getESData($productId, $storeId);

                    if ($this->elasticSearchClient->exists($eSData)) {
                        $this->elasticSearchClient->update(
                            $eSData,
                            $productData,
                            $hasChanges
                        );

                        if ($hasChanges && $currentCategoryIds = $this->productCategoryList->getCategoryIds($productId)) {
                            $categoryIds[$productId] = \array_unique($currentCategoryIds);
                        }
                    }
                }
            }

            $this->categoryCachePurger->purgeCache($categoryIds);
        }

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSortingData(): array
    {
        $sortingData = [];

        foreach ($this->collectorPool->getCollectors() as $attributeCode => $sortingDataCollector) {
            if (!$sortingDataCollector instanceof CollectorInterface) {
                throw new Exception($sortingDataCollector::class . ' must implement ' . CollectorInterface::class);
            }

            $currentSortingData = $sortingDataCollector->collect();
            $currentSortingData = $this->normalizeSortingData($currentSortingData, $attributeCode);

            $sortingData = \array_replace_recursive($sortingData, $currentSortingData);
        }

        \ksort($sortingData);

        return $sortingData;
    }

    /**
     * @param array $sortingData
     * @param string $attributeCode
     * @return array
     */
    private function normalizeSortingData(
        array $sortingData,
        string $attributeCode
    ): array {
        $normalizedData = [];

        foreach ($sortingData as $data) {
            if (!empty($data[CollectorInterface::PRODUCT_ID])) {
                $normalizedData[$data[CollectorInterface::PRODUCT_ID]] = [
                    CollectorInterface::PRODUCT_ID => $data[CollectorInterface::PRODUCT_ID],
                    $attributeCode => $data[$attributeCode]
                ];
            }
        }

        return $normalizedData;
    }

    /**
     * @return array
     */
    private function getStoreIds(): array
    {
        return \array_keys($this->storeManager->getStores());
    }
}
