<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;
use RuntimeException;

final class InMemoryStockIndicatorExportDocumentRepository implements StockIndicatorExportDocumentRepository
{
    /** @var StockIndicatorExportDocument[] */
    private $documents = [];

    public function save(StockIndicatorExportDocument $document): void
    {
        $this->documents[$document->documentId()->toString()] = $document;
    }

    /**
     * @param StockIndicatorExportDocumentId $documentId
     *
     * @return StockIndicatorExportDocument
     *
     * @throws RuntimeException
     */
    public function findById(StockIndicatorExportDocumentId $documentId): StockIndicatorExportDocument
    {
        if (!array_key_exists($documentId->toString(), $this->documents)) {
            throw new RuntimeException('document not found');
        }

        return $this->documents[$documentId->toString()];
    }
}
