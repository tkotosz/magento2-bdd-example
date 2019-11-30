<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use PhpSpec\ObjectBehavior;

final class StockSpec extends ObjectBehavior
{
    function it_can_be_created_from_a_positive_integer_value()
    {
        $this->beConstructedThrough('fromInt', [1]);

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(Stock::class);
    }

    function it_cannot_be_less_than_zero()
    {
        $this->beConstructedThrough('fromInt', [-1]);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(Stock::class);
    }
}
