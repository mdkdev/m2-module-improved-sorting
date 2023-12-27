<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Cache\Category\Type;

/**
 * Interface CacheTypeInterface
 * @package Mdkdev\ImprovedSorting\Model\Cache\Category\Type
 */
interface CacheTypeInterface
{
    /**
     * @param array $categoryIds
     * @return $this
     */
    public function purge(array $categoryIds): self;
}
