<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Hydrator;

use LupaSearch\LupaSearchPluginMSI\Model\Product\Hydrator\Source;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceCodeInterface;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceNameInterface;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    private Source $object;

    private MockObject $inventorySourceCode;

    private MockObject $inventorySourceName;

    private MockObject $product;

    public function testExtract(): void
    {
        $expected = [
            'sources' => [
                'Kaunas',
                'Vilnius',
            ],
        ];

        $this->product->expects($this->once())
            ->method('getId')
            ->willReturn('420');

        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn('6');

        $this->inventorySourceCode->expects($this->once())
            ->method('getByProductId')
            ->with(420)
            ->willReturn(['0112', '911']);

        $this->inventorySourceName->expects($this->once())
            ->method('getBySourceCodes')
            ->with(
                ['0112', '911'],
                6,
            )
            ->willReturn(
                [
                    '0112' => 'Kaunas',
                    '911' => 'Vilnius',
                ],
            );

        $this->assertEquals($expected, $this->object->extract($this->product));
    }

    public function testExtractEmpty(): void
    {
        $expected = ['sources' => []];

        $this->product->expects($this->once())
            ->method('getId')
            ->willReturn('420');

        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn('6');

        $this->inventorySourceCode->expects($this->once())
            ->method('getByProductId')
            ->with(420)
            ->willReturn([]);

        $this->inventorySourceName->expects($this->once())
            ->method('getBySourceCodes')
            ->with(
                [],
                6,
            )
            ->willReturn([]);

        $this->assertEquals($expected, $this->object->extract($this->product));
    }

    protected function setUp(): void
    {
        $this->inventorySourceCode = $this->createMock(InventorySourceCodeInterface::class);
        $this->inventorySourceName = $this->createMock(InventorySourceNameInterface::class);
        $this->product = $this->createMock(Product::class);

        $this->object = new Source($this->inventorySourceCode, $this->inventorySourceName);
    }
}
