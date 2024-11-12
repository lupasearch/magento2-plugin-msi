<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName;

use LupaSearch\LupaSearchPluginMSI\Model\StockIdByStoreIdResolverInterface;
use Magento\Inventory\Model\ResourceModel\Source\Collection;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory;
use Magento\Inventory\Model\ResourceModel\StockSourceLink;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;

use function sprintf;

class CollectionBuilder implements CollectionBuilderInterface
{
    private CollectionFactory $collectionFactory;

    private StockIdByStoreIdResolverInterface $stockIdByStoreIdResolver;

    public function __construct(
        CollectionFactory $collectionFactory,
        StockIdByStoreIdResolverInterface $stockIdByStoreIdResolver
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->stockIdByStoreIdResolver = $stockIdByStoreIdResolver;
    }

    public function build(int $storeId): Collection
    {
        $collection = $this->collectionFactory->create();
        $stockId = $this->stockIdByStoreIdResolver->execute($storeId);
        $collection->join(
            [StockSourceLink::TABLE_NAME_STOCK_SOURCE_LINK],
            sprintf(
                '`%1$s`.`%2$s` = `main_table`.`%2$s` AND `%1$s`.`%3$s` = %4$d',
                StockSourceLink::TABLE_NAME_STOCK_SOURCE_LINK,
                StockSourceLinkInterface::SOURCE_CODE,
                StockSourceLinkInterface::STOCK_ID,
                $stockId,
            ),
            [],
        );

        return $collection;
    }
}
