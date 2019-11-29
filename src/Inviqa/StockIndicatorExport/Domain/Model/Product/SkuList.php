<?php

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class SkuList implements IteratorAggregate
{
    /** @var Sku[] */
    private $skus;

    /**
     * @param Sku[] $skus
     *
     * @return SkuList
     */
    public static function fromSkus(array $skus): SkuList
    {
        return new self($skus);
    }

    public function has(Sku $sku): bool
    {
        return in_array($sku, $this->skus, false);
    }

    /**
     * @return Traversable|Sku[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->skus);
    }

    /**
     * @param Sku[] $skus
     */
    private function __construct(array $skus)
    {
        $this->skus = $skus;
    }
}
