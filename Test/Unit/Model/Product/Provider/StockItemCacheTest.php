<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Provider;

use LupaSearch\LupaSearchPluginMSI\Model\DataProvider\StockItemConfigurationProviderInterface;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\StockItemCache;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StockItemCacheTest extends TestCase
{
    private StockItemCache $object;

    private MockObject $stockItemConfigurationProvider;

    public function testByProductId(): void
    {
        $stockItem = $this->createMock(StockItemConfigurationInterface::class);

        $this->stockItemConfigurationProvider
            ->expects($this->exactly(2))
            ->method('getByProductId')
            ->willReturn($stockItem);

        $this->assertEquals($stockItem, $this->object->getByProductId(6));
        $this->object->flush();
        $this->assertEquals($stockItem, $this->object->getByProductId(6));
    }

    public function testByProductIds(): void
    {
        $items = [];
        $items[6] = $this->createMock(StockItemConfigurationInterface::class);
        $items[66] = $this->createMock(StockItemConfigurationInterface::class);

        $this->stockItemConfigurationProvider
            ->expects($this->exactly(2))
            ->method('getByProductIds')
            ->withConsecutive(
                [[6, 66]],
                [[]],
            )->willReturnOnConsecutiveCalls(
                $items,
                [],
            );

        $this->assertEquals($items, $this->object->getByProductIds([6, 66]));
        $this->assertEquals([6 => $items[6]], $this->object->getByProductIds([6]));
    }

    public function testWarmup(): void
    {
        $items = [];
        $items[6] = $this->createMock(StockItemConfigurationInterface::class);
        $items[66] = $this->createMock(StockItemConfigurationInterface::class);

        $this->stockItemConfigurationProvider
            ->expects($this->exactly(5))
            ->method('getByProductIds')
            ->withConsecutive(
                [[6, 66]],
                [[]],
                [[]],
                [[6, 66]],
                [[]],
            )->willReturnOnConsecutiveCalls(
                $items,
                [],
                [6 => $items[6]],
                $items,
                [],
            );

        $this->object->warmup([6, 66]);
        $this->assertEquals($items, $this->object->getByProductIds([6, 66]));
        $this->assertArrayHasKey(6, $this->object->getByProductIds([6]));
        $this->object->flush();
        $this->object->warmup([6, 66]);
        $this->assertArrayHasKey(6, $this->object->getByProductIds([6]));
    }

    protected function setUp(): void
    {
        $this->stockItemConfigurationProvider = $this->createMock(StockItemConfigurationProviderInterface::class);
        $this->object = new StockItemCache($this->stockItemConfigurationProvider);
    }
}
