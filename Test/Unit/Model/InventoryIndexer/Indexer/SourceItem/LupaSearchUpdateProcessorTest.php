<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Test\Unit\Model\InventoryIndexer\Indexer\SourceItem;

use LupaSearch\LupaSearchPluginMSI\Model\InventoryIndexer\Indexer\SourceItem\LupaSearchUpdateProcessor;
use LupaSearch\LupaSearchPlugin\Model\Indexer\Action\RowsInterface;
use Magento\InventoryIndexer\Model\GetProductsIdsToProcess;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LupaSearchUpdateProcessorTest extends TestCase
{
    private LupaSearchUpdateProcessor $subject;

    /**
     * @var RowsInterface&MockObject
     */
    private MockObject $rowsMock;

    /**
     * @var GetProductsIdsToProcess&MockObject
     */
    private MockObject $getProductIdsToProcessMock;

    public function testGetSortOrder(): void
    {
        $this->assertSame(100, $this->subject->getSortOrder());
    }

    public function testProcessEmptyIdList(): void
    {
        $saleableStatusesBeforeSync = ['sku' => [3 => true]];
        $saleableStatusesAfterSync = ['sku' => [3 => true]];

        $this->getProductIdsToProcessMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn([]);
        $this->rowsMock
            ->expects($this->never())
            ->method('execute');

        $this->subject->process($saleableStatusesBeforeSync, $saleableStatusesAfterSync);
    }

    public function testProcessIdList(): void
    {
        $saleableStatusesBeforeSync = ['sku' => [3 => false]];
        $saleableStatusesAfterSync = ['sku' => [3 => true]];

        $this->getProductIdsToProcessMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn([654321]);
        $this->rowsMock
            ->expects($this->once())
            ->method('execute');

        $this->subject->process($saleableStatusesBeforeSync, $saleableStatusesAfterSync);
    }

    protected function setUp(): void
    {
        $this->rowsMock = $this->createMock(RowsInterface::class);
        $this->getProductIdsToProcessMock = $this->createMock(GetProductsIdsToProcess::class);

        $this->subject = new LupaSearchUpdateProcessor($this->rowsMock, $this->getProductIdsToProcessMock);
    }
}
