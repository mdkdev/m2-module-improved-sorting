<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Cron;

use Exception;
use Mdkdev\ImprovedSorting\Handler\UpdateSortAttributes as UpdateSortAttributesHandler;

/**
 * Class UpdateSortAttributes
 * @package Mdkdev\ImprovedSorting\Cron
 */
class UpdateSortAttributes
{
    /**
     * @param UpdateSortAttributesHandler $updateSortAttributesHandler
     */
    public function __construct(private readonly UpdateSortAttributesHandler $updateSortAttributesHandler)
    {}

    /**
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {
        $this->updateSortAttributesHandler->update();
    }
}
