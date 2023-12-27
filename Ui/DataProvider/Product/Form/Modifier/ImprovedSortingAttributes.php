<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Mdkdev\ImprovedSorting\Model\Attributes;

/**
 * Class ImprovedSortingAttributes
 * @package Mdkdev\ImprovedSorting\Ui\DataProvider\Product\Form\Modifier
 */
class ImprovedSortingAttributes extends AbstractModifier
{
    /**
     * @param Attributes $attributes
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        private readonly Attributes $attributes,
        private readonly ArrayManager $arrayManager
    ) {}

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        foreach ($this->attributes->getAttributes() as $attribute) {
            $path = $this->arrayManager->findPath($attribute, $meta, null, 'children');
            $meta = $this->arrayManager->set(
                "{$path}/arguments/data/config/disabled",
                $meta,
                true
            );
        }

        return $meta;
    }
}
