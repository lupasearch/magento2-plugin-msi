<?php

namespace LupaSearch\LupaSearchPluginMSI\Model;

interface StockIdByStoreIdResolverInterface
{
    public function execute(int $storeId): int;
}
