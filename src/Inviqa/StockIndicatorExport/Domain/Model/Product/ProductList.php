<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

/** @implements IteratorAggregate<Product> */
final class ProductList implements IteratorAggregate
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

    /** @return Iterator|Product[] */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->products);
    }

    public function has(ProductSku $productSku): bool
    {
        foreach ($this->products as $product) {
            if ($product->sku()->equals($productSku)) {
                return true;
            }
        }

        return false;
    }

    /** @param Product[] $products */
    private function __construct(array $products)
    {
        $this->products = $products;
    }
}
