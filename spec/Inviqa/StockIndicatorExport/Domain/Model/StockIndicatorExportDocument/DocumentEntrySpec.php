<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use PhpSpec\ObjectBehavior;

class DocumentEntrySpec extends ObjectBehavior
{
    function it_can_be_created_from_sku_and_stock_indicator()
    {
        $this->beConstructedThrough(
            'fromSkuAndStockIndicator',
            [Sku::fromString('foo'), StockIndicator::red()]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(DocumentEntry::class);
    }
}
