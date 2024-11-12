<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\DataProvider;

use LupaSearch\LupaSearchPluginMSI\Model\DataProvider\StockItemConfigurationProvider;
use Magento\CatalogInventory\Api\Data\StockItemCollectionInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\InventoryConfiguration\Model\StockItemConfigurationFactory;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StockItemConfigurationProviderTest extends TestCase
{
    private StockItemConfigurationProvider $object;

    private MockObject $legacyStockItemCriteriaFactory;

    private MockObject $legacyStockItemRepository;

    private MockObject $stockItemConfigurationFactory;

    public function testGetByProductId(): void
    {
        $items = [];
        $items[0] = $this->createMock(StockItemInterface::class);
        $items[0]->expects($this->once())
            ->method('getProductId')
            ->willReturn(2);

        $expected = $this->createMock(StockItemConfigurationInterface::class);

        $collection = $this->createMock(StockItemCollectionInterface::class);
        $collection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->legacyStockItemRepository
            ->expects($this->once())
            ->method('getList')
            ->willReturn($collection);

        $this->stockItemConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->withConsecutive(
                [
                    [
                        'stockItem' => $items[0]
                    ]
                ]
            )->willReturnOnConsecutiveCalls(
                $expected
            );

        $searchCriteria = $this->createMock(StockItemCriteriaInterface::class);

        $this->legacyStockItemCriteriaFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->legacyStockItemRepository
            ->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($collection);

        $this->assertEquals($expected, $this->object->getByProductId(2));
    }

    public function testGetByProductIdNull(): void
    {
        $collection = $this->createMock(StockItemCollectionInterface::class);
        $collection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->legacyStockItemRepository
            ->expects($this->once())
            ->method('getList')
            ->willReturn($collection);

        $searchCriteria = $this->createMock(StockItemCriteriaInterface::class);

        $this->legacyStockItemCriteriaFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->legacyStockItemRepository
            ->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($collection);

        $this->assertNull($this->object->getByProductId(6));
    }

    public function testGetByProductIds(): void
    {
        $items = [];
        $items[0] = $this->createMock(StockItemInterface::class);
        $items[0]->expects($this->once())
            ->method('getProductId')
            ->willReturn(2);
        $items[1] = $this->createMock(StockItemInterface::class);
        $items[1]->expects($this->once())
            ->method('getProductId')
            ->willReturn(3);

        $expected = [];
        $expected[2] = $this->createMock(StockItemConfigurationInterface::class);
        $expected[3] = $this->createMock(StockItemConfigurationInterface::class);

        $collection = $this->createMock(StockItemCollectionInterface::class);
        $collection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->legacyStockItemRepository
            ->expects($this->once())
            ->method('getList')
            ->willReturn($collection);

        $this->stockItemConfigurationFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [
                    [
                        'stockItem' => $items[0]
                    ]
                ],
                [
                    [
                        'stockItem' => $items[1]
                    ]
                ]
            )->willReturnOnConsecutiveCalls(
                ...$expected
            );

        $searchCriteria = $this->createMock(StockItemCriteriaInterface::class);

        $this->legacyStockItemCriteriaFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->legacyStockItemRepository
            ->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($collection);

        $this->assertEquals($expected, $this->object->getByProductIds([2, 3]));
    }

    public function testGetByProductIdsEmpty(): void
    {
        $this->assertEmpty($this->object->getByProductIds([]));
    }

    protected function setUp(): void
    {
        $this->legacyStockItemCriteriaFactory = $this->getMockBuilder(StockItemCriteriaInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->legacyStockItemRepository = $this->createMock(StockItemRepositoryInterface::class);
        $this->stockItemConfigurationFactory = $this->getMockBuilder(StockItemConfigurationFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->object = new StockItemConfigurationProvider(
            $this->legacyStockItemCriteriaFactory,
            $this->legacyStockItemRepository,
            $this->stockItemConfigurationFactory
        );
    }
}
