<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Provider;

use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceCodeCache;
use LupaSearch\LupaSearchPluginMSI\Model\ResourceModel\InventorySource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InventorySourceCodeCacheTest extends TestCase
{
    /**
     * @var InventorySourceCodeCache
     */
    private $object;

    /**
     * @var MockObject
     */
    private $inventorySource;

    public function testWarmup(): void
    {
        $productIds = [420, 113];
        $sourceCodes = [
            420 => [
                '0001',
                '0002',
            ],
            113 => [
                '0001',
                '0003',
            ],
        ];
        $this->inventorySource->expects(self::once())
            ->method('getAvailableSourceCodesByProductIds')
            ->with($productIds)
            ->willReturn($sourceCodes);

        $this->assertEquals([], $this->object->getByProductIds($productIds));
        $this->object->warmup($productIds);
        $this->assertEquals($sourceCodes, $this->object->getByProductIds($productIds));
        $this->assertEquals(
            [
                '0001',
                '0002',
            ],
            $this->object->getByProductId(420),
        );
        $this->assertEquals(
            [
                '0001',
                '0003',
            ],
            $this->object->getByProductId(113),
        );
        $this->assertEquals([], $this->object->getByProductId(6));
        $this->object->flush();
        $this->assertEquals([], $this->object->getByProductIds($productIds));
    }

    public function testGetByProductIdsEmpty(): void
    {
        $this->assertEquals([], $this->object->getByProductIds([420, 13]));
    }

    public function testGetByProductIdEmpty(): void
    {
        $this->assertEquals([], $this->object->getByProductId(420));
    }

    protected function setUp(): void
    {
        $this->inventorySource = $this->createMock(InventorySource::class);
        $this->object = new InventorySourceCodeCache($this->inventorySource);
    }
}
