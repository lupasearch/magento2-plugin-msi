<?php

declare(strict_types=1);

namespace LupaSearch\LupaSearchPluginMSI\Model\Indexer\Action;

use LupaSearch\LupaSearchPlugin\Model\Indexer\Action\RowsInterface;
use LupaSearch\LupaSearchPluginMSI\Model\ResourceModel\SourceItem as SourceItemResource;

class SourceItemRows implements RowsInterface
{
    private RowsInterface $rows;

    private SourceItemResource $sourceItemResource;

    public function __construct(RowsInterface $rows, SourceItemResource $sourceItemResource)
    {
        $this->rows = $rows;
        $this->sourceItemResource = $sourceItemResource;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $ids): void
    {
        $this->rows->execute($this->sourceItemResource->getProductIds($ids));
    }

    /**
     * @inheritDoc
     */
    public function executeByStore(int $storeId, array $ids): void
    {
        $this->rows->executeByStore($storeId, $this->sourceItemResource->getProductIds($ids));
    }
}
