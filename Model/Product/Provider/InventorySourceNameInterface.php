<?php

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider;

interface InventorySourceNameInterface
{
    public function getBySourceCode(string $sourceCode, int $storeId): ?string;

    /**
     * @param string[] $sourceCodes
     * @return string[]
     */
    public function getBySourceCodes(array $sourceCodes, int $storeId): array;

    /**
     * @return string[]
     */
    public function getAll(int $storeId): array;
}
