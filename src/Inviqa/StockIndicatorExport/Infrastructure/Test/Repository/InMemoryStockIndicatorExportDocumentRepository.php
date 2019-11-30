<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Test\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;
use RuntimeException;

final class InMemoryStockIndicatorExportDocumentRepository implements StockIndicatorExportDocumentRepository
{
    /** @var StockIndicatorExportDocument|null */
    private $lastDocument = null;

    public function save(StockIndicatorExportDocument $document): void
    {
        $this->lastDocument = $document;
    }

    public function getLast(): StockIndicatorExportDocument
    {
        if ($this->lastDocument === null) {
            throw new RuntimeException('Document not found');
        }

        return $this->lastDocument;
    }

    public function clear(): void
    {
        $this->lastDocument = null;
    }
}
