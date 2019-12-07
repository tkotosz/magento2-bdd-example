<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Exception\StockIndicatorExportDocumentSaveFailedException;

interface StockIndicatorExportDocumentRepository
{
    /**
     * @param StockIndicatorExportDocument $document
     *
     * @throws StockIndicatorExportDocumentSaveFailedException
     */
    public function save(StockIndicatorExportDocument $document): void;
}
