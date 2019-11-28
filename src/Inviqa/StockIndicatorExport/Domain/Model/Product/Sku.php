<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;

final class Sku
{
    /** @var string */
    private $value;

    public static function fromString(string $value): Sku
    {
        if ($value === "") {
            throw new InvalidArgumentException('Product sku cannot be empty');
        }

        if (strpos($value, ' ') !== false) {
            throw new InvalidArgumentException('Product sku cannot contain spaces');
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    private function __construct(string $value)
    {
        $this->value = $value;
    }
}
