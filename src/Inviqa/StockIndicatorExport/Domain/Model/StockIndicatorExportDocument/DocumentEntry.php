<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;

final class DocumentEntry
{
    /** @var Sku */
    private $sku;

    /** @var StockIndicator */
    private $stockIndicator;

    public static function fromSkuAndStockIndicator(Sku $sku, StockIndicator $stockIndicator): DocumentEntry
    {
        return new self($sku, $stockIndicator);
    }

    public function sku(): Sku
    {
        return $this->sku;
    }

    public function stockIndicator(): StockIndicator
    {
        return $this->stockIndicator;
    }

    private function __construct(Sku $sku, StockIndicator $stockIndicator)
    {
        $this->sku = $sku;
        $this->stockIndicator = $stockIndicator;
    }
}
