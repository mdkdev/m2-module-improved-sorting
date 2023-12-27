<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Collector;

/**
 * Interface CollectorInterface
 * @package Mdkdev\ImprovedSorting\Model\Collector
 */
interface CollectorInterface
{
    const PRODUCT_ID = 'product_id';

    const DEFAULT_VALUE_TYPE_DECIMAL = 0.0000;
    const DEFAULT_VALUE_TYPE_INT = 0;

    /**
     * Method must return an array with key => value pairs where product_id is mandatory. Example:
     * [
     *     [
     *          'product_id' => 1,
     *          'qty_ordered' => 100
     *     ],
     *     [
     *          'product_id' => 2,
     *          'qty_ordered' => 110
     *     ]
     * ]
     * @return array
     */
    public function collect(): array;

    /**
     * Ge default value when attribute value is not (yet) available.
     *
     * @return mixed
     */
    public function getDefaultValue(): mixed;
}
