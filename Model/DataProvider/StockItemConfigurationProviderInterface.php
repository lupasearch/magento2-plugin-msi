<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\DataProvider;

use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;

interface StockItemConfigurationProviderInterface
{
    public function getByProductId(int $productId): ?StockItemConfigurationInterface;

    /**
     * @param int[] $productIds
     * @return StockItemConfigurationInterface[]
     */
    public function getByProductIds(array $productIds): array;
}
