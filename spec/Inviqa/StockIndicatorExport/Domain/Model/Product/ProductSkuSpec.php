<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use PhpSpec\ObjectBehavior;

final class ProductSkuSpec extends ObjectBehavior
{
    function it_can_be_created_from_a_valid_string()
    {
        $this->beConstructedThrough('fromString', ['foo']);

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(ProductSku::class);
    }

    function it_cannot_be_empty()
    {
        $this->beConstructedThrough('fromString', ['']);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_cannot_contain_spaces()
    {
        $this->beConstructedThrough('fromString', ['foo bar']);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
