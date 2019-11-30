<?php

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

class CsvStockIndicatorExportDocumentRepository implements StockIndicatorExportDocumentRepository
{
    public function save(StockIndicatorExportDocument $document): void
    {
        $fp = fopen('/tmp/file.csv', 'w');

        foreach ($document as $entry) {
            fputcsv($fp, [$entry->sku()->toString(), $entry->stockIndicator()->toString()]);
        }

        fclose($fp);
    }
}
