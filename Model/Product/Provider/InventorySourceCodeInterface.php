<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider;

interface InventorySourceCodeInterface
{
    /**
     * @param int $productId
     * @return string[]
     */
    public function getByProductId(int $productId): array;

    /**
     * @param int[] $productIds
     * @return array<string[]>
     */
    public function getByProductIds(array $productIds): array;
}
