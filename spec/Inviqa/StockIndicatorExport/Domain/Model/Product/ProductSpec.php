<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\Product;

use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductStock;
use PhpSpec\ObjectBehavior;

final class ProductSpec extends ObjectBehavior
{
    function it_can_be_created_from_sku()
    {
        $this->beConstructedThrough(
            'fromSku',
            [ProductSku::fromString('foo')]
        );

        $this->shouldHaveType(Product::class);
    }

    function it_can_be_created_from_sku_and_stock()
    {
        $this->beConstructedThrough(
            'fromSkuAndStock',
            [ProductSku::fromString('foo'), ProductStock::fromInt(10)]
        );

        $this->shouldHaveType(Product::class);
    }
}
