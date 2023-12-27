<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Collector;

use Magento\Framework\App\ResourceConnection as AppResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class ResourceConnection
 * @package Mdkdev\ImprovedSorting\Model\Collector
 */
class ResourceConnection
{
    /**
     * @param AppResourceConnection $resourceConnection
     */
    public function __construct(private readonly AppResourceConnection $resourceConnection)
    {}

    /**
     * @return AdapterInterface
     */
    public function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }
}
