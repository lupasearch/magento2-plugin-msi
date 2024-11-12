<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\DataProvider;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\Stock;
use Magento\InventoryConfiguration\Model\StockItemConfigurationFactory;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;

class StockItemConfigurationProvider implements StockItemConfigurationProviderInterface
{
    private StockItemCriteriaInterfaceFactory $legacyStockItemCriteriaFactory;

    private StockItemRepositoryInterface $legacyStockItemRepository;

    private StockItemConfigurationFactory $stockItemConfigurationFactory;

    public function __construct(
        StockItemCriteriaInterfaceFactory $legacyStockItemCriteriaFactory,
        StockItemRepositoryInterface $legacyStockItemRepository,
        StockItemConfigurationFactory $stockItemConfigurationFactory
    ) {
        $this->legacyStockItemCriteriaFactory = $legacyStockItemCriteriaFactory;
        $this->legacyStockItemRepository = $legacyStockItemRepository;
        $this->stockItemConfigurationFactory = $stockItemConfigurationFactory;
    }

    public function getByProductId(int $productId): ?StockItemConfigurationInterface
    {
        $items = $this->getByProductIds([$productId]);

        return $items[$productId] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getByProductIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $searchCriteria = $this->legacyStockItemCriteriaFactory->create();
        $searchCriteria->setProductsFilter($productIds);
        $searchCriteria->addFilter(StockItemInterface::STOCK_ID, StockItemInterface::STOCK_ID, Stock::DEFAULT_STOCK_ID);

        $stockItemCollection = $this->legacyStockItemRepository->getList($searchCriteria);

        $items = [];

        foreach ($stockItemCollection->getItems() as $stockItem) {
            $items[$stockItem->getProductId()] = $this->stockItemConfigurationFactory->create(
                [
                    'stockItem' => $stockItem
                ]
            );
        }

        return $items;
    }
}

