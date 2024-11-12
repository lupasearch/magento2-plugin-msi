<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\ResourceModel;

use LupaSearch\LupaSearchPluginMSI\Model\ResourceModel\InventorySource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Platform\Quote;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Select\ColumnsRenderer;
use Magento\Framework\DB\Select\DistinctRenderer;
use Magento\Framework\DB\Select\ForUpdateRenderer;
use Magento\Framework\DB\Select\FromRenderer;
use Magento\Framework\DB\Select\GroupRenderer;
use Magento\Framework\DB\Select\HavingRenderer;
use Magento\Framework\DB\Select\LimitRenderer;
use Magento\Framework\DB\Select\OrderRenderer;
use Magento\Framework\DB\Select\SelectRenderer;
use Magento\Framework\DB\Select\UnionRenderer;
use Magento\Framework\DB\Select\WhereRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @phpcs:disable Magento2.Files.LineLength.MaxExceeded,Generic.Files.LineLength.TooLong,SlevomatCodingStandard.Files.LineLength.LineTooLong
 */
class InventorySourceTest extends TestCase
{
    private InventorySource $object;

    private MockObject $resourceConnection;

    private MockObject $connection;

    public function testGetAvailableSourceCodesByProductIds(): void
    {
        $expected = <<<SQL
SELECT `source_item`.`source_code`, `product`.`entity_id` FROM `inventory_source_item` AS `source_item`
 INNER JOIN `catalog_product_entity` AS `product` ON `source_item`.`sku` = `product`.`sku`
 INNER JOIN `inventory_source` AS `source` ON `source`.`source_code` = `source_item`.`source_code` WHERE (`source_item`.`status` = 1) AND (`source_item`.`quantity` > 0) AND (`source`.`enabled` = 1) AND (`product`.`entity_id` IN(420, 666))
SQL;

        $this->connection->expects($this->once())
            ->method('fetchAll')
            ->willReturnCallback(function (Select $sql) use ($expected): array {
                $this->assertEquals($expected, (string)$sql);

                return [
                    ['entity_id' => '420', 'source_code' => 'xxxx'],
                    ['entity_id' => '420', 'source_code' => 'xxxx2'],
                    ['entity_id' => '666', 'source_code' => 'zzz'],
                    [],
                ];
            });

        $this->assertEquals(
            [
                420 => ['xxxx', 'xxxx2'],
                666 => ['zzz'],
            ],
            $this->object->getAvailableSourceCodesByProductIds([420, 666]),
        );
    }

    public function testGetAvailableSourceSelect(): void
    {
        $expected = <<<SQL
SELECT `source_item`.`source_code`, `product`.`entity_id` FROM `inventory_source_item` AS `source_item`
 INNER JOIN `catalog_product_entity` AS `product` ON `source_item`.`sku` = `product`.`sku`
 INNER JOIN `inventory_source` AS `source` ON `source`.`source_code` = `source_item`.`source_code` WHERE (`source_item`.`status` = 1) AND (`source_item`.`quantity` > 0) AND (`source`.`enabled` = 1)
SQL;
        $select = $this->object->getAvailableSourceSelect();
        $this->assertEquals($expected, $select->__toString());
    }

    public function testGetSourceCodeSelect(): void
    {
        $expected = <<<SQL
SELECT `source_item`.`source_code`, `product`.`entity_id` FROM `inventory_source_item` AS `source_item`
 INNER JOIN `catalog_product_entity` AS `product` ON `source_item`.`sku` = `product`.`sku`
SQL;
        $select = $this->object->getSourceCodeSelect();
        $this->assertEquals($expected, $select->__toString());
    }

    protected function setUp(): void
    {
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->connection = $this->getMockBuilder(Mysql::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['select', '_connect', 'fetchAll'])
            ->getMock();
        $selectRenderer = new SelectRenderer($this->getRenderers());
        $select = new Select($this->connection, $selectRenderer);

        $this->resourceConnection->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connection);

        $this->resourceConnection->expects($this->any())
            ->method('getTableName')
            ->willReturnCallback(static fn (string $table): string => $table);

        $this->connection->expects($this->any())
            ->method('select')
            ->willReturn($select);

        $this->object = new InventorySource($this->resourceConnection);
    }

    /**
     * @return array<array<int|string|object>>
     */
    private function getRenderers(): array
    {
        $quote = new Quote();

        return [
            'distinct' => [
                'renderer' => new DistinctRenderer(),
                'sort' => 100,
                'part' => 'distinct',
            ],
            'columns' => [
                'renderer' => new ColumnsRenderer($quote),
                'sort' => 200,
                'part' => 'columns',
            ],
            'union' => [
                'renderer' => new UnionRenderer(),
                'sort' => 300,
                'part' => 'union',
            ],
            'from' => [
                'renderer' => new FromRenderer($quote),
                'sort' => 400,
                'part' => 'from',
            ],
            'where' => [
                'renderer' => new WhereRenderer(),
                'sort' => 500,
                'part' => 'where',
            ],
            'group' => [
                'renderer' => new GroupRenderer($quote),
                'sort' => 600,
                'part' => 'group',
            ],
            'having' => [
                'renderer' => new HavingRenderer(),
                'sort' => 700,
                'part' => 'having',
            ],
            'order' => [
                'renderer' => new OrderRenderer($quote),
                'sort' => 800,
                'part' => 'order',
            ],
            'limit' => [
                'renderer' => new LimitRenderer(),
                'sort' => 900,
                'part' => 'limitcount',
            ],
            'for_update' => [
                'renderer' => new ForUpdateRenderer(),
                'sort' => 1000,
                'part' => 'forupdate',
            ],
        ];
    }
}
