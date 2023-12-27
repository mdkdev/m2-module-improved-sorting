<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Cache\Category\Type;

use Magento\CacheInvalidate\Model\PurgeCache;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Cache\Tag\Resolver;

/**
 * Class Varnish
 * @package Mdkdev\ImprovedSorting\Model\Cache\Category\Type
 */
class Varnish implements CacheTypeInterface
{
    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param PurgeCache $purgeCache
     * @param Resolver $tagResolver
     */
    public function __construct(
        private readonly CategoryCollectionFactory $categoryCollectionFactory,
        private readonly PurgeCache $purgeCache,
        private readonly Resolver $tagResolver
    ) {}

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function purge(array $categoryIds): self
    {
        $categoryCollection = $this->getCategoryCollection($categoryIds);
        $cacheTags = $this->getCacheTags($categoryCollection);

        if ($cacheTags) {
            $this->purgeCache->sendPurgeRequest($cacheTags);
        }

        return $this;
    }

    /**
     * @param array $categoryIds
     * @return array
     */
    private function getCategoryCollection(array $categoryIds): array
    {
        $categoryIds = \array_unique(\array_merge(...$categoryIds));

        return $this->categoryCollectionFactory
            ->create()
            ->addFieldToFilter('entity_id', $categoryIds)
            ->getItems();
    }

    /**
     * @param array $categoryCollection
     * @return array
     */
    private function getCacheTags(array $categoryCollection): array
    {
        $bareTags = [];

        foreach ($categoryCollection as $category) {
            $categoryBareTags = $this->tagResolver->getTags($category);
            $bareTags = \array_merge($bareTags, $categoryBareTags);
        }

        $tags = [];
        $pattern = '((^|,)%s(,|$))';

        foreach ($bareTags as $tag) {
            $tags[] = \sprintf($pattern, $tag);
        }

        return $tags;
    }
}
