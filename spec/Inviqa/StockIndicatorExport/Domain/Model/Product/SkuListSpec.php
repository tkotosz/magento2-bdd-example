<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use PhpSpec\ObjectBehavior;

class SkuListSpec extends ObjectBehavior
{
    function it_can_be_created_from_a_list_of_skus()
    {
        $this->beConstructedThrough(
            'fromSkus',
            [[Sku::fromString('foo'), Sku::fromString('bar')]]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(SkuList::class);
    }

    function it_can_check_if_a_sku_is_in_the_list()
    {
        $this->beConstructedThrough(
            'fromSkus',
            [[Sku::fromString('foo'), Sku::fromString('bar')]]
        );

        $this->has(Sku::fromString('foo'))->shouldReturn(true);
        $this->has(Sku::fromString('bar'))->shouldReturn(true);
        $this->has(Sku::fromString('baz'))->shouldReturn(false);
    }
}
