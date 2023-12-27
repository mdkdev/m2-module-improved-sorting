<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validator\ValidateException;
use Mdkdev\Core\Helper\ConvertStringToPhrase;
use Mdkdev\ImprovedSorting\Model\Attributes;

/**
 * Class AddImprovedSortingAttributes
 * @package Mdkdev\ImprovedSorting\Setup\Patch\Data
 */
class AddImprovedSortingAttributes implements DataPatchInterface
{
    private const GROUP_ID = 'Sorting';

    private ?EavSetup $eavSetup;
    private array $attributeSetIds = [];
    private ?string $entityTypeId;

    /**
     * @param AttributeGroupInterfaceFactory $attributeGroupFactory
     * @param AttributeGroupRepositoryInterface $attributeGroupRepository
     * @param Attributes $attributes
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @throws LocalizedException
     */
    public function __construct(
        private readonly AttributeGroupInterfaceFactory $attributeGroupFactory,
        private readonly AttributeGroupRepositoryInterface $attributeGroupRepository,
        private readonly Attributes $attributes,
        private readonly EavSetupFactory $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->entityTypeId = $this->eavSetup->getEntityTypeId(Product::ENTITY);
        $this->attributeSetIds = $this->eavSetup->getAllAttributeSetIds($this->entityTypeId);
    }

    /**
     * @return $this
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function apply(): self
    {
        $attributeGroupIds = $this->createAndResolveAttributeGroups();
        $this->installAttributes($attributeGroupIds);

        return $this;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws LocalizedException
     */
    private function createAndResolveAttributeGroups(): array
    {
        $attributeGroupIds = [];

        foreach ($this->attributeSetIds as $attributeSetId) {
            $attributeGroupId = $this->eavSetup->getAttributeGroupId(
                $this->entityTypeId,
                $attributeSetId,
                self::GROUP_ID
            );

            if (!$attributeGroupId) {
                $attributeGroup = $this->attributeGroupFactory->create();
                $attributeGroup->setAttributeSetId($attributeSetId);
                $attributeGroup->setAttributeGroupName(self::GROUP_ID);
                $attributeGroup = $this->attributeGroupRepository->save($attributeGroup);

                $attributeGroupId = $attributeGroup->getAttributeGroupId();
            }

            $attributeGroupIds[$attributeSetId] = $attributeGroupId;
        }

        return $attributeGroupIds;
    }

    /**
     * @param array $attributeGroupIds
     * @return void
     * @throws LocalizedException
     * @throws ValidateException
     */
    private function installAttributes(array $attributeGroupIds): void
    {
        foreach ($this->getAttributesData() as $attributeCode => $attributeData) {
            $this->eavSetup->addAttribute(
                Product::ENTITY,
                $attributeCode,
                $attributeData
            );

            foreach ($this->attributeSetIds as $attributeSetId) {
                $this->eavSetup->addAttributeToGroup(
                    $this->entityTypeId,
                    $attributeSetId,
                    $attributeGroupIds[$attributeSetId],
                    $attributeCode
                );
            }
        }
    }

    /**
     * @return array[]
     */
    private function getAttributesData(): array
    {
        foreach ($this->attributes->getDefaultAttributes() as $attributeToAdd) {
            $attributeData[$attributeToAdd] = [
                'type' => 'int',
                'label' => ConvertStringToPhrase::convert($attributeToAdd),
                'input' => 'text',
                'required' => false,
                'user_defined' => true,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'global' => ScopedAttributeInterface::SCOPE_STORE
            ];
        }

        return $attributeData ?? [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
