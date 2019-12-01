<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class SkuList implements IteratorAggregate
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

    /**
     * @param string[] $skusAsStrings
     *
     * @return SkuList
     */
    public static function fromStrings(array $skusAsStrings): SkuList
    {
        $skus = [];

        foreach ($skusAsStrings as $sku) {
            $skus[] = Sku::fromString($sku);
        }

        return self::fromSkus($skus);
    }

    public function has(Sku $otherSku): bool
    {
        foreach ($this->skus as $sku) {
            if ($sku->toString() === $otherSku->toString()) {
                return true;
            }
        }

        return false;
    }

    public function toStrings(): array
    {
        return array_map(function (Sku $sku) {
            return $sku->toString();
        }, $this->skus);
    }

    /**
     * @return Traversable|Sku[]
     */
    public function getIterator(): Traversable
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
