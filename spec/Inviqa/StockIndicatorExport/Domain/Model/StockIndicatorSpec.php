<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use PhpSpec\ObjectBehavior;

class StockIndicatorSpec extends ObjectBehavior
{
    function it_can_be_created_from_red_stock_indicator_string()
    {
        $this->beConstructedThrough('fromString', ['RED']);

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicator::class);
    }

    function it_can_be_created_from_yellow_stock_indicator_string()
    {
        $this->beConstructedThrough('fromString', ['YELLOW']);

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicator::class);
    }

    function it_can_be_created_from_green_stock_indicator_string()
    {
        $this->beConstructedThrough('fromString', ['GREEN']);

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicator::class);
    }

    function it_cannot_be_created_from_invalid_stock_indicator_string()
    {
        $this->beConstructedThrough('fromString', ['BLUE']);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(StockIndicator::class);
    }

    function it_can_be_created_as_red_indicator()
    {
        $this->beConstructedThrough('red');

        $this->shouldHaveType(StockIndicator::class);
    }

    function it_can_be_created_as_yellow_indicator()
    {
        $this->beConstructedThrough('yellow');

        $this->shouldHaveType(StockIndicator::class);
    }

    function it_can_be_created_as_green_indicator()
    {
        $this->beConstructedThrough('green');

        $this->shouldHaveType(StockIndicator::class);
    }

    function it_can_compare_itself_with_another_stock_indicator()
    {
        $this->beConstructedThrough('red');

        $this->sameAs(StockIndicator::red())->shouldReturn(true);
        $this->sameAs(StockIndicator::green())->shouldReturn(false);
        $this->sameAs(StockIndicator::yellow())->shouldReturn(false);
    }

    function it_is_created_as_a_red_stock_indicator_when_created_for_an_out_of_stock_product()
    {
        $product = Product::fromSkuAndStock(Sku::fromString('foo'), Stock::fromInt(0));

        $this->beConstructedThrough('for', [$product]);

        $this->shouldHaveType(StockIndicator::class);
        $this->sameAs(StockIndicator::red())->shouldReturn(true);
    }

    function it_is_created_as_a_yellow_stock_indicator_when_created_for_a_product_with_low_stock()
    {
        $product = Product::fromSkuAndStock(Sku::fromString('foo'), Stock::fromInt(5));

        $this->beConstructedThrough('for', [$product]);

        $this->shouldHaveType(StockIndicator::class);
        $this->sameAs(StockIndicator::yellow())->shouldReturn(true);
    }

    function it_is_created_as_a_green_stock_indicator_when_created_for_a_product_with_high_stock()
    {
        $product = Product::fromSkuAndStock(Sku::fromString('foo'), Stock::fromInt(20));

        $this->beConstructedThrough('for', [$product]);

        $this->shouldHaveType(StockIndicator::class);
        $this->sameAs(StockIndicator::green())->shouldReturn(true);
    }
}
