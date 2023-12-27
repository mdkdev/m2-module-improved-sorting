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
 * Class Storefront
 * @package Mdkdev\ImprovedSorting\Config
 */
class Storefront extends ScopeConfig
{
    private const XML_PATH_CLEAN_CACHE = 'improved_sorting/storefront/attribute_sort_mapping';

    /**
     * @return array
     */
    public function getAttributeSortMapping(): array
    {
        return $this->getJsonDecodedValue(self::XML_PATH_CLEAN_CACHE);
    }
}
