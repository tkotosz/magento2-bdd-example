<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use PhpSpec\ObjectBehavior;

final class ProductListSpec extends ObjectBehavior
{
    function it_can_be_created_from_a_list_of_products()
    {
        $this->beConstructedThrough(
            'fromProducts',
            [[Product::fromSkuAndStock(Sku::fromString('foo'), Stock::fromInt(10))]]
        );

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(ProductList::class);
    }
}
