<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName;

use Magento\Inventory\Model\ResourceModel\Source\Collection;

interface CollectionBuilderInterface
{
    public function build(int $storeId): Collection;
}
