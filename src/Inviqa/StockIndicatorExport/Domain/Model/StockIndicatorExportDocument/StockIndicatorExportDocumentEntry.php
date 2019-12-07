<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;

final class StockIndicatorExportDocumentEntry
{
    /** @var ProductSku */
    private $sku;

    /** @var StockIndicator */
    private $stockIndicator;

    public static function fromSkuAndStockIndicator(ProductSku $sku, StockIndicator $stockIndicator): StockIndicatorExportDocumentEntry
    {
        return new self($sku, $stockIndicator);
    }

    public static function fromProduct(Product $product): StockIndicatorExportDocumentEntry
    {
        return self::fromSkuAndStockIndicator($product->sku(), StockIndicator::fromProductStock($product->stock()));
    }

    public function sku(): ProductSku
    {
        return $this->sku;
    }

    public function stockIndicator(): StockIndicator
    {
        return $this->stockIndicator;
    }

    private function __construct(ProductSku $sku, StockIndicator $stockIndicator)
    {
        $this->sku = $sku;
        $this->stockIndicator = $stockIndicator;
    }
}
