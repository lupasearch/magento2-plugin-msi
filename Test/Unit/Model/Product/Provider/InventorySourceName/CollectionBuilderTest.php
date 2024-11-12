<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\Product\Provider\InventorySourceName;

use LupaSearch\LupaSearchPluginMSI\Model\Product\Provider\InventorySourceName\CollectionBuilder;
use LupaSearch\LupaSearchPluginMSI\Model\StockIdByStoreIdResolverInterface;
use Magento\Inventory\Model\ResourceModel\Source\Collection;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectionBuilderTest extends TestCase
{
    /**
     * @var CollectionBuilder
     */
    private $object;

    /**
     * @var MockObject
     */
    private $collectionFactory;

    /**
     * @var MockObject
     */
    private $stockIdByStoreIdResolver;

    /**
     * @var MockObject
     */
    private $collection;

    public function testBuild(): void
    {
        $this->collectionFactory->expects(self::once())
            ->method('create')
            ->willReturn($this->collection);

        $this->stockIdByStoreIdResolver->expects(self::once())
            ->method('execute')
            ->with(1)
            ->willReturn(6);

        $cond = '`inventory_source_stock_link`.`source_code` = `main_table`.`source_code` AND ';
        $cond .= '`inventory_source_stock_link`.`stock_id` = 6';

        $this->collection->expects(self::any())
            ->method('join')
            ->with(
                ['inventory_source_stock_link'],
                $cond,
            );

        $this->object->build(1);
    }

    protected function setUp(): void
    {
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->stockIdByStoreIdResolver = $this->createMock(StockIdByStoreIdResolverInterface::class);
        $this->collection = $this->createMock(Collection::class);

        $this->object = new CollectionBuilder($this->collectionFactory, $this->stockIdByStoreIdResolver);
    }
}
