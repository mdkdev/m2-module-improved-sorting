<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mdkdev\ImprovedSorting\Model\Collector\CollectorInterface;
use Mdkdev\ImprovedSorting\Model\Collector\CollectorPool;

/**
 * Class ImprovedSorting
 * @package Mdkdev\ImprovedSorting\Model\ResourceModel
 */
class ImprovedSorting extends AbstractDb
{
    public const TABLE_NAME = 'mdkdev_improved_sorting';
    protected $_idFieldName = 'product_id';
    private array $queryResult = [];

    /**
     * @param CollectorPool $collectorPool
     * @param Context $context
     * @param $connectionName
     */
    public function __construct(
        private readonly CollectorPool $collectorPool,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct(
            $context,
            $connectionName
        );
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, $this->_idFieldName);
    }

    /**
     * @param int $entityId
     * @param string $attributeCode
     * @return mixed
     */
    public function getAttributeValue(
        int $entityId,
        string $attributeCode
    ): mixed {
        if (empty($this->queryResult[$entityId])) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->getTable(self::TABLE_NAME))
                ->where($this->_idFieldName . '= ?', $entityId);

            if ($result = $connection->fetchRow($select)) {
                $this->queryResult[$entityId] = $result;
            }
        }

        return $this->queryResult[$entityId][$attributeCode] ?? $this->getDefaultValue($attributeCode);
    }

    /**
     * @param string $attributeCode
     * @return mixed
     */
    private function getDefaultValue(string $attributeCode): mixed
    {
        $collectorPool = $this->collectorPool->getCollectors();

        if (\count($collectorPool) && isset($collectorPool[$attributeCode])) {
            /** @var CollectorInterface $collector */
            $collector = $collectorPool[$attributeCode];
            $value = $collector->getDefaultValue();
        }

        return $value ?? 0;
    }

    /**
     * @param array $sortingData
     * @return void
     */
    public function refreshSortingTable(array $sortingData): void
    {
        $connection = $this->getConnection();
        $connection->truncateTable(self::TABLE_NAME);

        $connection->insertMultiple(
            self::TABLE_NAME,
            $sortingData
        );
    }
}
