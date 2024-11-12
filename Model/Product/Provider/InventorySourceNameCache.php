<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider;

use LupaSearch\LupaSearchPlugin\Model\Provider\ProviderCacheInterface;
use Magento\Store\Model\Store;

use function array_flip;
use function array_intersect_key;

/**
 * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
 */
class InventorySourceNameCache implements InventorySourceNameInterface, ProviderCacheInterface
{
    private InventorySourceName $inventorySourceName;

    /**
     * @var array<string, string>
     */
    private array $names = [];

    public function __construct(InventorySourceName $inventorySourceName)
    {
        $this->inventorySourceName = $inventorySourceName;
    }

    public function getBySourceCode(string $sourceCode, int $storeId): ?string
    {
        return $this->names[$sourceCode] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getBySourceCodes(array $sourceCodes, int $storeId): array
    {
        return array_intersect_key($this->names, array_flip($sourceCodes));
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $storeId): array
    {
        return $this->names;
    }

    /**
     * @inheritDoc
     */
    public function warmup(array $ids, ?int $storeId = null): void
    {
        $this->names = $this->inventorySourceName->getAll($storeId ?? Store::DEFAULT_STORE_ID);
    }

    public function flush(): void
    {
        $this->names = [];
    }
}
