<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Config;

use Mdkdev\Core\Config\ScopeConfig;

/**
 * Class General
 * @package Mdkdev\ImprovedSorting\Config
 */
class General extends ScopeConfig
{
    private const XML_PATH_CLEAN_CACHE = 'improved_sorting/general/clean_cache';

    /**
     * @return bool
     */
    public function isCleanCacheEnabled(): bool
    {
        return $this->isSetFlag(self::XML_PATH_CLEAN_CACHE);
    }
}
