<?php

namespace Inviqa\StockIndicatorExport\Infrastructure\Test\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

class InMemoryStockIndicatorExportDocumentRepository implements StockIndicatorExportDocumentRepository
{
    /** @var StockIndicatorExportDocument|null */
    private $lastDocument = null;

    public function save(StockIndicatorExportDocument $document): void
    {
        $this->lastDocument = $document;
    }

    public function getLast(): ?StockIndicatorExportDocument
    {
        return $this->lastDocument;
    }

    public function clear(): void
    {
        $this->lastDocument = null;
    }
}
