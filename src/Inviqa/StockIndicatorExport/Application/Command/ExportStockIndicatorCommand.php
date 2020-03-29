<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Application\Command;

use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;

final class ExportStockIndicatorCommand
{
    /** @var string */
    private $documentId;

    /** @var string */
    private $productSku;

    public function __construct(string $documentId, string $productSku)
    {
        $this->documentId = $documentId;
        $this->productSku = $productSku;
    }

    public function documentId(): StockIndicatorExportDocumentId
    {
        return StockIndicatorExportDocumentId::fromString($this->documentId);
    }

    public function productSku(): ProductSku
    {
        return ProductSku::fromString($this->productSku);
    }
}
