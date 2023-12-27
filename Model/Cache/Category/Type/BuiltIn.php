<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Cache\Category\Type;

use Magento\PageCache\Model\Cache\Type as BuildInCache;
use Zend_Cache;

/**
 * Class BuiltIn
 * @package Mdkdev\ImprovedSorting\Model\Cache\Category\Type
 */
class BuiltIn implements CacheTypeInterface
{
    private const PAGE_CACHE_CATEGORY_PREFIX = 'CAT_C_';

    /**
     * @param BuildInCache $builtInCache
     */
    public function __construct(
        private readonly BuildInCache $builtInCache,
    ) {}

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function purge(array $categoryIds): self
    {
        $categoryIds = \array_unique(\array_merge(...$categoryIds));
        $cacheTags = $this->getCacheTags($categoryIds);

        if ($cacheTags) {
            $this->builtInCache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, $cacheTags);
        }

        return $this;
    }

    /**
     * @param array $categoryIds
     * @return array
     */
    private function getCacheTags(array $categoryIds): array
    {
        return \array_map(function($categoryId) {
            return self::PAGE_CACHE_CATEGORY_PREFIX . $categoryId;
        }, $categoryIds);
    }
}
