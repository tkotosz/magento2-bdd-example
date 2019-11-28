<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;

final class Stock
{
    /** @var int */
    private $value;

    public static function fromInt(int $value): Stock
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Stock level cannot be less than 0');
        }

        return new self($value);
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
