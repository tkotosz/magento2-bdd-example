<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

final class CsvStockIndicatorExportDocumentRepository implements StockIndicatorExportDocumentRepository
{
    public function save(StockIndicatorExportDocument $document): void
    {
        // TODO
        $fp = fopen('/tmp/file.csv', 'w');

        if ($fp === false) {
            return;
        }

        foreach ($document as $entry) {
            fputcsv($fp, [$entry->sku()->toString(), $entry->stockIndicator()->toString()]);
        }

        fclose($fp);
    }
}
