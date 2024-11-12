<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider;

use LupaSearch\LupaSearchPlugin\Model\Provider\ProviderCacheInterface;
use LupaSearch\LupaSearchPluginMSI\Model\ResourceModel\InventorySource;

use function array_flip;
use function array_intersect_key;

class InventorySourceCodeCache implements InventorySourceCodeInterface, ProviderCacheInterface
{
    private InventorySource $inventorySource;

    /**
     * @var array<int, string[]>
     */
    private array $sourceCodes = [];

    public function __construct(InventorySource $inventorySource)
    {
        $this->inventorySource = $inventorySource;
    }

    /**
     * @inheritDoc
     */
    public function getByProductId(int $productId): array
    {
        return $this->sourceCodes[$productId] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getByProductIds(array $productIds): array
    {
        return array_intersect_key($this->sourceCodes, array_flip($productIds));
    }

    /**
     * @inheritDoc
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed
     */
    public function warmup(array $ids, ?int $storeId = null): void
    {
        $this->sourceCodes = $this->inventorySource->getAvailableSourceCodesByProductIds($ids);
    }

    public function flush(): void
    {
        $this->sourceCodes = [];
    }
}
