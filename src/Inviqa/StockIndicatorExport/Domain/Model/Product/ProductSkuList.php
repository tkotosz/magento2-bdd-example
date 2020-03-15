<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

/** @implements IteratorAggregate<ProductSku> */
final class ProductSkuList implements IteratorAggregate
{
    /** @var ProductSku[] */
    private $skus;

    /**
     * @param ProductSku[] $skus
     *
     * @return ProductSkuList
     */
    public static function fromSkus(array $skus): ProductSkuList
    {
        return new self($skus);
    }

    public function has(ProductSku $otherSku): bool
    {
        foreach ($this->skus as $sku) {
            if ($sku->toString() === $otherSku->toString()) {
                return true;
            }
        }

        return false;
    }

    /** @return Iterator|ProductSku[] */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->skus);
    }

    /** @return string[] */
    public function toStringArray(): array
    {
        $skus = [];

        foreach ($this->skus as $sku) {
            $skus[] = $sku->toString();
        }

        return $skus;
    }

    /** @param ProductSku[] $skus */
    private function __construct(array $skus)
    {
        $this->skus = $skus;
    }
}
