<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model;

/**
 * Class Attributes
 * @package Mdkdev\ImprovedSorting\Model
 */
class Attributes
{
    public const ATTRIBUTE_QTY_ORDERED = 'qty_ordered';
    public const ATTRIBUTE_QTY_VIEWED = 'qty_viewed';

    private const DEFAULT_ATTRIBUTES = [
        self::ATTRIBUTE_QTY_ORDERED,
        self::ATTRIBUTE_QTY_VIEWED
    ];

    /**
     * @param array $attributes
     */
    public function __construct(private readonly array $attributes = [])
    {}

    /**
     * @return string[]
     */
    public function getDefaultAttributes(): array
    {
        return self::DEFAULT_ATTRIBUTES;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return \array_merge($this->getDefaultAttributes(), $this->attributes);
    }
}
