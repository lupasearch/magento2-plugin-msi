<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider;

use LupaSearch\LupaSearchPluginMSI\Model\DataProvider\StockItemConfigurationProviderInterface;
use LupaSearch\LupaSearchPlugin\Model\Provider\ProviderCacheInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;

use function array_key_exists;

class StockItemCache implements StockItemConfigurationProviderInterface, ProviderCacheInterface
{
    /**
     * @var StockItemConfigurationInterface|null[]
     */
    private array $items = [];

    private StockItemConfigurationProviderInterface $stockItemConfigurationProvider;

    public function __construct(StockItemConfigurationProviderInterface $stockItemConfigurationProvider)
    {
        $this->stockItemConfigurationProvider = $stockItemConfigurationProvider;
    }

    public function getByProductId(int $productId): ?StockItemConfigurationInterface
    {
        if (array_key_exists($productId, $this->items)) {
            return $this->items[$productId];
        }

        $this->items[$productId] = $this->stockItemConfigurationProvider->getByProductId($productId);

        return $this->items[$productId];
    }

    /**
     * @inheritDoc
     */
    public function getByProductIds(array $productIds): array
    {
        $items = [];
        $fetchIds = [];

        foreach ($productIds as $id) {
            if (!array_key_exists($id, $this->items)) {
                $fetchIds[] = $id;

                continue;
            }

            if (isset($this->items[$id])) {
                $items[$id] = $this->items[$id];
            }
        }

        foreach ($this->stockItemConfigurationProvider->getByProductIds($fetchIds) as $id => $item) {
            $this->items[$id] = $item;
            $items[$id] = $item;
        }

        return $items;
    }

    /**
     * @inheritDoc
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     */
    public function warmup(array $ids, ?int $storeId = null): void
    {
        $this->getByProductIds($ids);
    }

    public function flush(): void
    {
        $this->items = [];
    }
}
