<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider;

use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName\CollectionBuilderInterface;

use function array_flip;
use function array_intersect_key;

class InventorySourceName implements InventorySourceNameInterface
{
    private CollectionBuilderInterface $collectionBuilder;

    public function __construct(CollectionBuilderInterface $collectionBuilder)
    {
        $this->collectionBuilder = $collectionBuilder;
    }

    public function getBySourceCode(string $sourceCode, int $storeId): ?string
    {
        return $this->getAll($storeId)[$sourceCode] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getBySourceCodes(array $sourceCodes, int $storeId): array
    {
        return array_intersect_key($this->getAll($storeId), array_flip($sourceCodes));
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $storeId): array
    {
        $collection = $this->collectionBuilder->build($storeId);
        $names = [];

        foreach ($collection as $item) {
            $names[$item->getSourceCode()] = $item->getName();
        }

        return $names;
    }
}
