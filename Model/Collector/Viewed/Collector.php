<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Collector\Viewed;

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
                    'COALESCE(COUNT(rvpi.product_id), 0) AS qty_viewed'
                ]
            )->joinLeft(
                ['rvpi' => $connection->getTableName('report_viewed_product_index')],
                'cpe.entity_id = rvpi.product_id',
                []
            )->group('cpe.entity_id');

        return $connection->fetchAll($query);
    }

    /**
     * @return int
     */
    public function getDefaultValue(): int
    {
        return self::DEFAULT_VALUE_TYPE_INT;
    }
}
