<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Cache\Category;

use Exception;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Mdkdev\ImprovedSorting\Config\General as GeneralConfig;
use Mdkdev\ImprovedSorting\Model\Cache\Category\Type\CacheTypeInterface;

/**
 * Class Purge
 * @package Mdkdev\ImprovedSorting\Model\Cache\Category
 */
class Purge
{
    /**
     * @param GeneralConfig $generalConfig
     * @param PageCacheConfig $pageCacheConfig
     * @param array $cachePool
     */
    public function __construct(
        private readonly GeneralConfig $generalConfig,
        private readonly PageCacheConfig $pageCacheConfig,
        private readonly array $cachePool = [],
    ) {}

    /**
     * @param array $categoryIds
     * @return void
     * @throws Exception
     */
    public function purgeCache(array $categoryIds): void
    {
        if (\count($categoryIds)
            && $this->generalConfig->isCleanCacheEnabled()
            && $this->pageCacheConfig->isEnabled()
        ) {
            $cacheType = $this->pageCacheConfig->getType();

            if (isset($this->cachePool[$cacheType])) {
                $cacheProcessor = $this->cachePool[$cacheType];

                if (!$cacheProcessor instanceof CacheTypeInterface) {
                    throw new Exception($cacheProcessor::class . ' must implement ' . CacheTypeInterface::class);
                }

                $cacheProcessor->purge($categoryIds);
            }
        }
    }
}
