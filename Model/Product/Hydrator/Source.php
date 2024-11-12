<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Hydrator;

use LupaSearch\LupaSearchPlugin\Model\Hydrator\ProductHydratorInterface;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceCodeInterface;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceNameInterface;
use Magento\Catalog\Model\Product;

use function array_values;

class Source implements ProductHydratorInterface
{
    private InventorySourceCodeInterface $inventorySourceCode;

    private InventorySourceNameInterface $inventorySourceName;

    public function __construct(
        InventorySourceCodeInterface $inventorySourceCode,
        InventorySourceNameInterface $inventorySourceName
    ) {
        $this->inventorySourceCode = $inventorySourceCode;
        $this->inventorySourceName = $inventorySourceName;
    }

    /**
     * @inheritDoc
     */
    public function extract(Product $product): array
    {
        $productId = (int)$product->getId();
        $storeId = (int)$product->getStoreId();

        return [
            'sources' => array_values($this->inventorySourceName->getBySourceCodes(
                $this->inventorySourceCode->getByProductId($productId),
                $storeId,
            )),
        ];
    }
}
