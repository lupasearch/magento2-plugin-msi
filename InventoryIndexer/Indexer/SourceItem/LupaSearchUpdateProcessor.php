<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\InventoryIndexer\Indexer\SourceItem;

use LupaSearch\LupaSearchPlugin\Model\Indexer\Action\RowsInterface;
use Magento\InventoryIndexer\Indexer\SourceItem\CompositeProductProcessorInterface;
use Magento\InventoryIndexer\Model\GetProductsIdsToProcess;

class LupaSearchUpdateProcessor implements CompositeProductProcessorInterface
{
    private RowsInterface $rows;

    private GetProductsIdsToProcess $getProductsIdsToProcess;

    private int $sortOrder;

    public function __construct(
        RowsInterface $rows,
        GetProductsIdsToProcess $getProductsIdsToProcess,
        int $sortOrder = 100
    ) {
        $this->sortOrder = $sortOrder;
        $this->getProductsIdsToProcess = $getProductsIdsToProcess;
        $this->rows = $rows;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @inheritdoc
     */
    public function process(
        array $saleableStatusesBeforeSync,
        array $saleableStatusesAfterSync
    ): void {
        $productsIdsToProcess = $this->getProductsIdsToProcess->execute(
            $saleableStatusesBeforeSync,
            $saleableStatusesAfterSync,
        );

        if (empty($productsIdsToProcess)) {
            return;
        }

        $this->rows->execute($productsIdsToProcess);
    }
}
