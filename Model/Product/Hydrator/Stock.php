<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Hydrator;

use LupaSearch\LupaSearchPlugin\Model\Hydrator\ProductHydratorInterface;
use LupaSearch\LupaSearchPluginMSI\Model\DataProvider\StockItemConfigurationProviderInterface;
use Magento\Catalog\Model\Product;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;

class Stock implements ProductHydratorInterface
{
    public const IN_STOCK = 'IN_STOCK';
    public const OUT_OF_STOCK = 'OUT_OF_STOCK';

    private IsProductSalableInterface $isProductSalable;

    private StockByWebsiteIdResolverInterface $stockByWebsiteId;

    private StockItemConfigurationProviderInterface $stockItemConfigurationProvider;

    public function __construct(
        IsProductSalableInterface $isProductSalable,
        StockByWebsiteIdResolverInterface $stockByWebsiteId,
        StockItemConfigurationProviderInterface $stockItemConfigurationProvider
    ) {
        $this->isProductSalable = $isProductSalable;
        $this->stockByWebsiteId = $stockByWebsiteId;
        $this->stockItemConfigurationProvider = $stockItemConfigurationProvider;
    }

    /**
     * @inheritDoc
     */
    public function extract(Product $product): array
    {
        $websiteId = (int)$product->getStore()->getWebsiteId();
        $stockId = (int)$this->stockByWebsiteId->execute($websiteId)->getStockId();
        $isSalable = $this->isProductSalable->execute($product->getSku(), $stockId);
        $stockItemConfiguration = $this->stockItemConfigurationProvider->getByProductId((int)$product->getId());

        return [
            'stock_status' => $isSalable ? self::IN_STOCK : self::OUT_OF_STOCK,
            'in_stock' => $isSalable,
            'use_qty_increments' => $stockItemConfiguration && $stockItemConfiguration->isEnableQtyIncrements(),
            'qty_increments' => $stockItemConfiguration ? $stockItemConfiguration->getQtyIncrements() : 0,
            'is_out_of_stock' => $isSalable ? 0 : 1,
        ];
    }
}
