<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Hydrator;

use LupaSearch\LupaSearchPluginMSI\Model\DataProvider\StockItemConfigurationProviderInterface;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Hydrator\Stock;
use Magento\Catalog\Model\Product;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StockTest extends TestCase
{
    private Stock $object;

    private MockObject $isProductSalable;

    private MockObject $stockByWebsiteId;

    private MockObject $stockItemConfigurationProvider;

    private MockObject $store;

    private MockObject $stock;

    private MockObject $product;

    private int $websiteId = 3;

    private int $stockId = 1;

    public function testExtract(): void
    {
        $stockItemConfiguration = $this->createMock(StockItemConfigurationInterface::class);
        $stockItemConfiguration
            ->expects($this->once())
            ->method('isEnableQtyIncrements')
            ->willReturn(true);
        $stockItemConfiguration
            ->expects($this->once())
            ->method('getQtyIncrements')
            ->willReturn(10.0);

        $this->product
            ->expects($this->once())
            ->method('getSku')
            ->willReturn('test1');

        $this->product
            ->expects($this->once())
            ->method('getId')
            ->willReturn('6');

        $this->isProductSalable
            ->expects($this->once())
            ->method('execute')
            ->with('test1', $this->stockId)
            ->willReturn(true);

        $this->stockItemConfigurationProvider
            ->expects($this->once())
            ->method('getByProductId')
            ->with(6)
            ->willReturn($stockItemConfiguration);

        $result = $this->object->extract($this->product);

        $this->assertEquals('IN_STOCK', $result['stock_status']);
        $this->assertEquals(true, $result['in_stock']);
        $this->assertEquals(true, $result['use_qty_increments']);
        $this->assertEquals(10.0, $result['qty_increments']);
    }

    public function testExtractNoStock(): void
    {
        $this->product
            ->expects($this->once())
            ->method('getSku')
            ->willReturn('test2');

        $this->product
            ->expects($this->once())
            ->method('getId')
            ->willReturn('66');

        $this->isProductSalable
            ->expects($this->once())
            ->method('execute')
            ->with('test2', $this->stockId)
            ->willReturn(false);

        $this->stockItemConfigurationProvider
            ->expects($this->once())
            ->method('getByProductId')
            ->with(66)
            ->willReturn(null);

        $result = $this->object->extract($this->product);

        $this->assertEquals('OUT_OF_STOCK', $result['stock_status']);
        $this->assertEquals(false, $result['in_stock']);
        $this->assertEquals(false, $result['use_qty_increments']);
        $this->assertEquals(0, $result['qty_increments']);
    }

    protected function setUp(): void
    {
        $this->isProductSalable = $this->createMock(IsProductSalableInterface::class);
        $this->stockByWebsiteId = $this->createMock(StockByWebsiteIdResolverInterface::class);
        $this->stockItemConfigurationProvider = $this->createMock(StockItemConfigurationProviderInterface::class);
        $this->store = $this->createMock(Store::class);
        $this->product = $this->createMock(Product::class);
        $this->stock = $this->createMock(StockInterface::class);

        $this->product
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->store);

        $this->store
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($this->websiteId);

        $this->stockByWebsiteId
            ->expects($this->once())
            ->method('execute')
            ->with($this->websiteId)
            ->willReturn($this->stock);

        $this->stock
            ->expects($this->once())
            ->method('getStockId')
            ->willReturn($this->stockId);

        $this->object = new Stock(
            $this->isProductSalable,
            $this->stockByWebsiteId,
            $this->stockItemConfigurationProvider,
        );
    }
}
