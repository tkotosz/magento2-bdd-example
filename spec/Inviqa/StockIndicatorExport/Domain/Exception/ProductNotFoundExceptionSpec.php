<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Exception;

use Exception;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use PhpSpec\ObjectBehavior;

class ProductNotFoundExceptionSpec extends ObjectBehavior
{
    function it_can_be_created_from_product_sku()
    {
        $this->beConstructedThrough('fromSku', [Sku::fromString('foo')]);

        $this->shouldHaveType(ProductNotFoundException::class);
        $this->getMessage()->shouldReturn('Product \'foo\' does not exists');
    }

    function it_can_be_created_from_product_sku_and_previous_error()
    {
        $previousException = new Exception('error');
        $this->beConstructedThrough('fromSkuAndPreviousError', [Sku::fromString('foo'), $previousException]);

        $this->shouldHaveType(ProductNotFoundException::class);
        $this->getPrevious()->shouldReturn($previousException);
    }
}
