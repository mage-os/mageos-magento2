<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model;

use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Store\Model\Store;

class StockItemProcessor implements StockItemProcessorInterface
{
    /**
     * @var StockItemImporterInterface
     */
    private $stockItemImporter;

    /**
     * @param StockItemImporterInterface $stockItemImporter
     */
    public function __construct(
        StockItemImporterInterface $stockItemImporter
    ) {
        $this->stockItemImporter = $stockItemImporter;
    }

    /**
     * @inheritdoc
     */
    public function process(array $stockData, array $importedData): void
    {
        $importStockData = [];
        foreach ($stockData as $sku => $productStockData) {
            if (isset($stockData[Product::SCOPE_DEFAULT])) {
                $importStockData[$sku] = $productStockData[Product::SCOPE_DEFAULT];
            } else {
                $importStockData[$sku] = reset($productStockData);
            }
        }
        $this->stockItemImporter->import($importStockData);
    }
}
