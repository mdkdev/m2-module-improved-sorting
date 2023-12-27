<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Direction
 * @package Mdkdev\ImprovedSorting\Model\Config\Source
 */
class Direction implements OptionSourceInterface
{
    private const OPTIONS_VALUE = 'value';
    private const OPTIONS_LABEL = 'label';

    public const DIRECTION_ASC = 'ASC';
    public const DIRECTION_DESC = 'DESC';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                self::OPTIONS_VALUE => self::DIRECTION_ASC,
                self::OPTIONS_LABEL => __(self::DIRECTION_ASC)
            ],
            [
                self::OPTIONS_VALUE => self::DIRECTION_DESC,
                self::OPTIONS_LABEL => __(self::DIRECTION_DESC)
            ]
        ];
    }
}
