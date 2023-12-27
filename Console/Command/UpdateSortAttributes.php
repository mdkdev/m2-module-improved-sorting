<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Console\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Mdkdev\ImprovedSorting\Handler\UpdateSortAttributes as UpdateSortAttributesHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateSortAttributes
 * @package Mdkdev\ImprovedSorting\Console\Command
 */
class UpdateSortAttributes extends Command
{
    /**
     * @param State $appState
     * @param UpdateSortAttributesHandler $updateSortAttributesHandler
     * @param string|null $name
     */
    public function __construct(
        private readonly State $appState,
        private readonly UpdateSortAttributesHandler $updateSortAttributesHandler,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('mdkdev:improved-sorting:update');
        $this->setDescription('Update sorting attributes in ElasticSearch/OpenSearch.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'executeEmulated'],
            [$input, $output]
        );

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    public function executeEmulated(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->updateSortAttributesHandler->update();
    }
}
