<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Plugin\Magento\Catalog\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Mdkdev\ImprovedSorting\Config\Storefront as StorefrontConfig;

/**
 * Class ToolbarPlugin
 * @package Mdkdev\ImprovedSorting\Plugin\Magento\Catalog\Block\Product\ProductList
 */
class ToolbarPlugin
{
    private array $defaultDirection = [];

    /**
     * @param StorefrontConfig $storefrontConfig
     */
    public function __construct(private readonly StorefrontConfig $storefrontConfig)
    {}

    /**
     * @param Toolbar $subject
     * @return void
     */
    public function beforeGetCurrentDirection(Toolbar $subject): void
    {
        $currentSort = $subject->getCurrentOrder();

        if (\array_key_exists($currentSort, $this->defaultDirection)) {
            if ($this->defaultDirection[$currentSort] === null) {
                return;
            }

            $subject->setDefaultDirection($this->defaultDirection[$currentSort]);

            return;
        }

        if ($defaultDirection = $this->getDefaultDirectionByCurrentOrder($currentSort)) {
            $this->defaultDirection[$currentSort] = $defaultDirection;
        } else {
            $this->defaultDirection[$currentSort] = null;
        }

        if (!empty($this->defaultDirection[$currentSort])) {
            $subject->setDefaultDirection($this->defaultDirection[$currentSort]);
        }
    }

    /**
     * @param string $currentOrder
     * @return string
     */
    private function getDefaultDirectionByCurrentOrder(string $currentOrder): string
    {
        if ($attributeSortMapping = $this->storefrontConfig->getAttributeSortMapping()) {
            foreach ($attributeSortMapping as $attributeSortMap) {
                if ($attributeSortMap->getSortAttribute() === $currentOrder) {
                    $defaultDirection = $attributeSortMap->getSortDirection();

                    break;
                }
            }
        }

        return $defaultDirection ?? '';
    }
}
