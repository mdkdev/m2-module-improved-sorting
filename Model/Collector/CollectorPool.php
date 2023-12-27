<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Collector;

/**
 * Class CollectorPool
 * @package Mdkdev\ImprovedSorting\Model\Collector
 */
class CollectorPool
{
    /**
     * @param array $collectors
     */
    public function __construct(private readonly array $collectors = [])
    {}

    /**
     * @return array
     */
    public function getCollectors(): array
    {
        return $this->collectors;
    }
}
