<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;

final class ProductStock
{
    /** @var int */
    private $value;

    public static function fromInt(int $value): ProductStock
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Stock level cannot be less than 0');
        }

        return new self($value);
    }

    public static function outOfStock(): ProductStock
    {
        return new self(0);
    }

    public function toInt(): int
    {
        return $this->value;
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }
}
