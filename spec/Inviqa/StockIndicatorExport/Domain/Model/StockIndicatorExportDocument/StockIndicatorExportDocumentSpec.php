<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;
use IteratorAggregate;
use PhpSpec\ObjectBehavior;
use RuntimeException;

final class StockIndicatorExportDocumentSpec extends ObjectBehavior
{
    function it_can_be_created_from_document_id()
    {
        $this->beConstructedThrough(
            'fromDocumentId',
            [StockIndicatorExportDocumentId::fromString('doc-001')]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicatorExportDocument::class);
    }

    function it_can_be_created_from_document_entries()
    {
        $entries = [
            StockIndicatorExportDocumentEntry::fromSkuAndStockIndicator(
                ProductSku::fromString('foo'),
                StockIndicator::red()
            )
        ];

        $this->beConstructedThrough(
            'fromDocumentIdAndEntries',
            [StockIndicatorExportDocumentId::fromString('doc-001'), $entries]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicatorExportDocument::class);
    }

    function it_is_iterable()
    {
        $this->beConstructedThrough(
            'fromDocumentId',
            [StockIndicatorExportDocumentId::fromString('doc-001')]
        );

        $this->shouldImplement(IteratorAggregate::class);
    }

    function it_allows_to_add_entries()
    {
        $this->beConstructedThrough(
            'fromDocumentId',
            [StockIndicatorExportDocumentId::fromString('doc-001')]
        );

        $newEntry = StockIndicatorExportDocumentEntry::fromSkuAndStockIndicator(
            ProductSku::fromString('bar'),
            StockIndicator::red()
        );

        $this->addEntry($newEntry);

        $this->entries()->shouldContain($newEntry);
    }

    function it_can_check_that_an_entry_with_sku_exists()
    {
        $fooEntry = StockIndicatorExportDocumentEntry::fromSkuAndStockIndicator(
            ProductSku::fromString('foo'),
            StockIndicator::red()
        );
        $barEntry = StockIndicatorExportDocumentEntry::fromSkuAndStockIndicator(
            ProductSku::fromString('bar'),
            StockIndicator::red()
        );

        $this->beConstructedThrough(
            'fromDocumentIdAndEntries',
            [StockIndicatorExportDocumentId::fromString('doc-001'), [$fooEntry, $barEntry]]
        );

        $this->hasEntryWithSku(ProductSku::fromString('foo'))->shouldReturn(true);
        $this->hasEntryWithSku(ProductSku::fromString('baz'))->shouldReturn(false);
    }
}
