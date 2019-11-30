<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use IteratorAggregate;
use PhpSpec\ObjectBehavior;

final class StockIndicatorExportDocumentSpec extends ObjectBehavior
{
    function it_can_be_created_from_document_entries()
    {
        $this->beConstructedThrough(
            'fromEntries',
            [[DocumentEntry::fromSkuAndStockIndicator(Sku::fromString('foo'), StockIndicator::red())]]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicatorExportDocument::class);
    }

    function it_is_iterable()
    {
        $this->shouldImplement(IteratorAggregate::class);
    }
}
