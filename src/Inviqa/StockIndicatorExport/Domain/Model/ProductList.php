<?php

namespace Inviqa\StockIndicatorExport\Domain\Model;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class ProductList implements IteratorAggregate
{
    /** @var Product[] */
    private $products;

    /**
     * @param Product[] $products
     *
     * @return ProductList
     */
    public static function fromProducts(array $products): ProductList
    {
        return new self($products);
    }

    /**
     * @return Traversable|Product[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->products);
    }

    /**
     * @param Product[] $products
     */
    private function __construct(array $products)
    {
        $this->products = $products;
    }
}