<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Inventory\Model\ResourceModel\Source;
use Magento\Inventory\Model\ResourceModel\SourceItem as SourceItemResourceModel;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;

use function sprintf;

class InventorySource
{
    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param int[] $ids
     * @return array<int, string[]>
     */
    public function getAvailableSourceCodesByProductIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $select = $this->getAvailableSourceSelect();
        $select->where('`product`.`entity_id` IN(?)', $ids);
        $connection = $this->resourceConnection->getConnection();
        $names = [];

        foreach ($connection->fetchAll($select) as $row) {
            $id = (int)($row['entity_id'] ?? 0);
            $sourceCode = $row[SourceInterface::SOURCE_CODE] ?? '';

            if (empty($id) || empty($sourceCode)) {
                continue;
            }

            $names[$id][] = $sourceCode;
        }

        return $names;
    }

    public function getAvailableSourceSelect(): Select
    {
        $sourceTable = $this->resourceConnection->getTableName(
            Source::TABLE_NAME_SOURCE,
        );

        $select = $this->getSourceCodeSelect();
        $select->joinInner(
            ['source' => $sourceTable],
            sprintf(
                '`source`.`%1$s` = `source_item`.`%2$s`',
                SourceInterface::SOURCE_CODE,
                SourceItemInterface::SOURCE_CODE,
            ),
            [],
        );
        $select->where(sprintf('`source_item`.`%1$s` = ?', SourceItemInterface::STATUS), 1);
        $select->where(sprintf('`source_item`.`%1$s` > ?', SourceItemInterface::QUANTITY), 0);
        $select->where(sprintf('`source`.`%1$s` = ?', SourceInterface::ENABLED), 1);

        return $select;
    }

    public function getSourceCodeSelect(): Select
    {
        $connection = $this->resourceConnection->getConnection();
        $sourceItemTable = $this->resourceConnection->getTableName(
            SourceItemResourceModel::TABLE_NAME_SOURCE_ITEM,
        );

        $select = $connection->select();
        $select->from(['source_item' => $sourceItemTable], [SourceInterface::SOURCE_CODE]);
        $select->joinInner(
            ['product' => 'catalog_product_entity'],
            sprintf('`source_item`.`%1$s` = `product`.`%1$s`', SourceItemInterface::SKU),
            ['entity_id'],
        );

        return $select;
    }
}
