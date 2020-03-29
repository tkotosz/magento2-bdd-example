<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Application\Command;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;

final class ExportAllStockIndicatorCommand
{
    /** @var string */
    private $documentId;

    public function __construct(string $documentId)
    {
        $this->documentId = $documentId;
    }

    public function documentId(): StockIndicatorExportDocumentId
    {
        return StockIndicatorExportDocumentId::fromString($this->documentId);
    }
}
