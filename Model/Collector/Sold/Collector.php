<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Collector\Sold;

use Mdkdev\ImprovedSorting\Model\Collector\CollectorInterface;
use Mdkdev\ImprovedSorting\Model\Collector\ResourceConnection;

/**
 * Class Collector
 * @package Mdkdev\ImprovedSorting\Model\Collector\Sold
 */
class Collector extends ResourceConnection implements CollectorInterface
{
    /**
     * @return array
     */
    public function collect(): array
    {
        $connection = $this->getConnection();
        $query = $this->getConnection()->select()
            ->from(
                ['cpe' => $connection->getTableName('catalog_product_entity')],
                [
                    'cpe.entity_id AS product_id',
                    'COALESCE(SUM(soi.qty_ordered - soi.qty_canceled), 0) AS qty_ordered'
                ]
            )->joinLeft(
                ['soi' => $connection->getTableName('sales_order_item')],
                'cpe.entity_id = soi.product_id',
                []
            )->group('cpe.entity_id');

        return $connection->fetchAll($query);
    }

    /**
     * @return float
     */
    public function getDefaultValue(): float
    {
        return self::DEFAULT_VALUE_TYPE_DECIMAL;
    }
}
