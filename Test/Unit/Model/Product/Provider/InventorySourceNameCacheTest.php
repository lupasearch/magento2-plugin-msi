<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Provider;

use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceNameCache;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InventorySourceNameCacheTest extends TestCase
{
    /**
     * @var InventorySourceNameCache
     */
    private $object;

    /**
     * @var MockObject
     */
    private $inventorySourceName;

    public function testWarmup(): void
    {
        $this->inventorySourceName->expects(self::once())
            ->method('getAll')
            ->with(1)
            ->willReturn(
                [
                    '02' => 'Vilnius',
                    '12' => 'Palanga',
                    '010' => 'Kaunas',
                ],
            );

        $this->assertNull($this->object->getBySourceCode('02', 1));
        $this->assertEquals([], $this->object->getBySourceCodes(['02', '12'], 1));
        $this->object->warmup([], 1);
        $this->assertEquals('Vilnius', $this->object->getBySourceCode('02', 1));
        $this->assertEquals(
            [
                '02' => 'Vilnius',
                '12' => 'Palanga',
            ],
            $this->object->getBySourceCodes(['02', '12'], 1),
        );
        $this->assertNull($this->object->getBySourceCode('15', 1));
        $this->assertEquals([], $this->object->getBySourceCodes(['15', '19'], 1));
        $this->object->flush();
        $this->assertNull($this->object->getBySourceCode('02', 1));
        $this->assertEquals([], $this->object->getBySourceCodes(['02', '12'], 1));
    }

    protected function setUp(): void
    {
        $this->inventorySourceName = $this->createMock(InventorySourceName::class);
        $this->object = new InventorySourceNameCache($this->inventorySourceName);
    }
}
