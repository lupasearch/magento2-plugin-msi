<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Provider;

use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName;
use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName\CollectionBuilderInterface;
use ArrayIterator;
use Magento\Inventory\Model\ResourceModel\Source\Collection;
use Magento\Inventory\Model\Source as SourceModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InventorySourceNameTest extends TestCase
{
    /**
     * @var InventorySourceName
     */
    private $object;

    /**
     * @var MockObject
     */
    private $collectionBuilder;

    public function testGetAll(): void
    {
        $data = [
            [
                'getSourceCode' => '0112',
                'getName' => 'Kaunas',
            ],
            [
                'getSourceCode' => '0113',
                'getName' => 'Vilnius',
            ],
            [
                'getSourceCode' => '122',
                'getName' => 'Palanga',
            ],
        ];
        $expected = [
            '0112' => 'Kaunas',
            '0113' => 'Vilnius',
            '122' => 'Palanga',
        ];

        $this->collectionBuilder->expects(self::any())
            ->method('build')
            ->willReturn($this->getCollection($data));

        $this->assertEquals($expected, $this->object->getAll(1));
    }

    public function testGetBySourceCode(): void
    {
        $data = [
            [
                'getSourceCode' => '0112',
                'getName' => 'Kaunas',
            ],
            [
                'getSourceCode' => '0113',
                'getName' => 'Vilnius',
            ],
            [
                'getSourceCode' => '122',
                'getName' => 'Palanga',
            ],
        ];

        $this->collectionBuilder->expects(self::any())
            ->method('build')
            ->with(1)
            ->willReturn($this->getCollection($data));

        $this->assertEquals('Palanga', $this->object->getBySourceCode('122', 1));
    }

    public function testGetBySourceCodeNoCode(): void
    {
        $data = [
            [
                'getSourceCode' => '0112',
                'getName' => 'Kaunas',
            ],
            [
                'getSourceCode' => '0113',
                'getName' => 'Vilnius',
            ],
            [
                'getSourceCode' => '122',
                'getName' => 'Palanga',
            ],
        ];

        $this->collectionBuilder->expects(self::any())
            ->method('build')
            ->with(1)
            ->willReturn($this->getCollection($data));

        $this->assertNull($this->object->getBySourceCode('123', 1));
    }

    public function testGetBySourceCodes(): void
    {
        $data = [
            [
                'getSourceCode' => '0112',
                'getName' => 'Kaunas',
            ],
            [
                'getSourceCode' => '0113',
                'getName' => 'Vilnius',
            ],
            [
                'getSourceCode' => '122',
                'getName' => 'Palanga',
            ],
        ];

        $this->collectionBuilder->expects(self::any())
            ->method('build')
            ->with(1)
            ->willReturn($this->getCollection($data));

        $expected = [
            '0113' => 'Vilnius',
            '122' => 'Palanga',
        ];

        $this->assertEquals($expected, $this->object->getBySourceCodes(['122', '0113'], 1));
    }

    public function testGetBySourceCodesOneCodeNotExist(): void
    {
        $data = [
            [
                'getSourceCode' => '0112',
                'getName' => 'Kaunas',
            ],
            [
                'getSourceCode' => '0113',
                'getName' => 'Vilnius',
            ],
            [
                'getSourceCode' => '122',
                'getName' => 'Palanga',
            ],
        ];

        $this->collectionBuilder->expects(self::any())
            ->method('build')
            ->with(1)
            ->willReturn($this->getCollection($data));

        $expected = [
            '0113' => 'Vilnius',
            '122' => 'Palanga',
        ];

        $this->assertEquals($expected, $this->object->getBySourceCodes(['122', '0113', '17'], 1));
    }

    public function testGetBySourceCodesNotExist(): void
    {
        $data = [
            [
                'getSourceCode' => '0112',
                'getName' => 'Kaunas',
            ],
            [
                'getSourceCode' => '0113',
                'getName' => 'Vilnius',
            ],
            [
                'getSourceCode' => '122',
                'getName' => 'Palanga',
            ],
        ];

        $this->collectionBuilder->expects(self::any())
            ->method('build')
            ->with(1)
            ->willReturn($this->getCollection($data));

        $this->assertEquals([], $this->object->getBySourceCodes(['2', '22', '55'], 1));
    }

    protected function setUp(): void
    {
        $this->collectionBuilder = $this->createMock(CollectionBuilderInterface::class);
        $this->object = new InventorySourceName($this->collectionBuilder);
    }

    /**
     * @param array<int, array<string, string>> $data
     */
    private function getCollection(array $data): MockObject
    {
        $mocks = [];

        foreach ($data as $modelData) {
            $mocks[] = $this->createSourceMock($modelData);
        }

        $collection = $this->createMock(Collection::class);
        $collection
            ->expects(self::once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator($mocks));

        return $collection;
    }

    /**
     * @param array<string, string> $modelData
     */
    private function createSourceMock(array $modelData): MockObject
    {
        $mock = $this->createMock(SourceModel::class);

        foreach ($modelData as $method => $value) {
            $mock->expects(self::any())->method($method)->willReturn($value);
        }

        return $mock;
    }
}
