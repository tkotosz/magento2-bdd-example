<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;
use PhpSpec\ObjectBehavior;

final class ProductSkuListSpec extends ObjectBehavior
{
    function it_can_be_created_from_a_list_of_skus()
    {
        $this->beConstructedThrough(
            'fromSkus',
            [[ProductSku::fromString('foo'), ProductSku::fromString('bar')]]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(ProductSkuList::class);
    }

    function it_can_check_if_a_sku_is_in_the_list()
    {
        $this->beConstructedThrough(
            'fromSkus',
            [[ProductSku::fromString('foo'), ProductSku::fromString('bar')]]
        );

        $this->has(ProductSku::fromString('foo'))->shouldReturn(true);
        $this->has(ProductSku::fromString('bar'))->shouldReturn(true);
        $this->has(ProductSku::fromString('baz'))->shouldReturn(false);
    }
}
