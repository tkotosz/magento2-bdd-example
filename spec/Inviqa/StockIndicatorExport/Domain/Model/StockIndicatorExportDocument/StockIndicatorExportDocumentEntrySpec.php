<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductStock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentEntry;
use PhpSpec\ObjectBehavior;

final class StockIndicatorExportDocumentEntrySpec extends ObjectBehavior
{
    function it_can_be_created_from_sku_and_stock_indicator()
    {
        $this->beConstructedThrough(
            'fromSkuAndStockIndicator',
            [ProductSku::fromString('foo'), StockIndicator::red()]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicatorExportDocumentEntry::class);
    }

    function it_can_be_created_from_product()
    {
        $this->beConstructedThrough(
            'fromProduct',
            [Product::fromSku(ProductSku::fromString('foo'))]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicatorExportDocumentEntry::class);
    }
}
