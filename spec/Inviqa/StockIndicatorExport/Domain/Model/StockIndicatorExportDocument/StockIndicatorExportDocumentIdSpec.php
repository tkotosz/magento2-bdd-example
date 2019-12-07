<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;
use PhpSpec\ObjectBehavior;

class StockIndicatorExportDocumentIdSpec extends ObjectBehavior
{
    function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['doc-001']);

        $this->shouldNotThrow(InvalidArgumentException::class);
        $this->shouldHaveType(StockIndicatorExportDocumentId::class);
    }
}
