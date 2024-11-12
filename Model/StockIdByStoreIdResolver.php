<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model;

use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class StockIdByStoreIdResolver implements StockIdByStoreIdResolverInterface
{
    private StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver;

    private StoreManagerInterface $storeManager;

    public function __construct(
        StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->storeManager = $storeManager;
    }

    public function execute(int $storeId): int
    {
        $websiteId = (int)$this->storeManager->getStore($storeId)->getWebsiteId();
        $stock = $this->stockByWebsiteIdResolver->execute($websiteId);

        return (int)$stock->getStockId();
    }
}
