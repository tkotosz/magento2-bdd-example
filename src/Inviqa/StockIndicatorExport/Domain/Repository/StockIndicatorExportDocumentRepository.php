<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Repository;

use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

interface StockIndicatorExportDocumentRepository
{
    public function save(StockIndicatorExportDocument $document): void;
}
