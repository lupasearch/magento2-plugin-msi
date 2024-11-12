<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Conditions;

use LupaSearch\LupaSearchPlugin\Model\Config\ProductConfigInterface;
use LupaSearch\LupaSearchPluginMSI\Model\StockIdByStoreIdResolverInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\CollectionModifierInterface;
use Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;

class Quantity implements CollectionModifierInterface
{
    private AddStockDataToCollection $addStockDataToCollection;

    private DefaultStockProviderInterface $defaultStockProvider;

    private StockIdByStoreIdResolverInterface $stockIdByStoreIdResolver;

    private ProductConfigInterface $productConfig;

    public function __construct(
        AddStockDataToCollection $addStockDataToCollection,
        DefaultStockProviderInterface $defaultStockProvider,
        StockIdByStoreIdResolverInterface $stockIdByStoreIdResolver,
        ProductConfigInterface $productConfig
    ) {
        $this->addStockDataToCollection = $addStockDataToCollection;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->stockIdByStoreIdResolver = $stockIdByStoreIdResolver;
        $this->productConfig = $productConfig;
    }

    public function apply(AbstractDb $abstractCollection): void
    {
        if (!$abstractCollection instanceof Collection) {
            return;
        }

        $storeId = (int)$abstractCollection->getStoreId();
        $stockId = $this->stockIdByStoreIdResolver->execute($storeId);
        $this->addStockDataToCollection->execute(
            $abstractCollection,
            $this->productConfig->isFilterInStock($storeId),
            $stockId,
        );
        $columnName = $stockId === $this->defaultStockProvider->getId() ? 'qty' : 'quantity';
        $abstractCollection->getSelect()->columns(['quantity' => 'stock_status_index.' . $columnName]);
    }
}
