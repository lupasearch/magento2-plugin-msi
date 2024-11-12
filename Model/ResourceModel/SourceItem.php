<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Inventory\Model\ResourceModel\SourceItem as SourceItemResourceModel;

use function array_map;

class SourceItem
{
    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param int[] $sourceItemIds
     * @return int[]
     */
    public function getProductIds(array $sourceItemIds): array
    {
        $connection = $this->resourceConnection->getConnection();
        $sourceItemTable = $this->resourceConnection->getTableName(
            SourceItemResourceModel::TABLE_NAME_SOURCE_ITEM,
        );

        $select = $connection->select();
        $select->from(['source_item' => $sourceItemTable], []);
        $select->joinInner(['product' => 'catalog_product_entity'], 'source_item.sku = product.sku', ['entity_id']);
        $select->where('source_item_id IN(?)', $sourceItemIds);

        return array_map('intval', $connection->fetchCol($select));
    }
}
