<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Model;

use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use PhpSpec\ObjectBehavior;

final class StockIndicatorSpec extends ObjectBehavior
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

    function it_can_be_created_from_zero_stock()
    {
        $this->beConstructedThrough('fromProductStock', [Stock::fromInt(0)]);

        $this->shouldHaveType(StockIndicator::class);
        $this->shouldBeLike(StockIndicator::red());
    }

    function it_can_be_created_from_low_stock()
    {
        $this->beConstructedThrough('fromProductStock', [Stock::fromInt(5)]);

        $this->shouldHaveType(StockIndicator::class);
        $this->shouldBeLike(StockIndicator::yellow());
    }

    function it_can_be_created_from_high_stock()
    {
        $this->beConstructedThrough('fromProductStock', [Stock::fromInt(20)]);

        $this->shouldHaveType(StockIndicator::class);
        $this->shouldBeLike(StockIndicator::green());
    }
}
