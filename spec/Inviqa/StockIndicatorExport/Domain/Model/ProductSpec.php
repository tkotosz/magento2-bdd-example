<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model;

use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use PhpSpec\ObjectBehavior;

class ProductSpec extends ObjectBehavior
{
    function it_can_be_created_from_sku()
    {
        $this->beConstructedThrough(
            'fromSku',
            [Sku::fromString('foo')]
        );

        $this->shouldHaveType(Product::class);
    }

    function it_can_be_created_from_sku_and_stock()
    {
        $this->beConstructedThrough(
            'fromSkuAndStock',
            [Sku::fromString('foo'), Stock::fromInt(10)]
        );

        $this->shouldHaveType(Product::class);
    }
}
