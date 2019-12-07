<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductStock;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductList;
use PhpSpec\ObjectBehavior;

final class ProductListSpec extends ObjectBehavior
{
    function it_can_be_created_from_a_list_of_products()
    {
        $this->beConstructedThrough(
            'fromProducts',
            [[Product::fromSkuAndStock(ProductSku::fromString('foo'), ProductStock::fromInt(10))]]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(ProductList::class);
    }
}
