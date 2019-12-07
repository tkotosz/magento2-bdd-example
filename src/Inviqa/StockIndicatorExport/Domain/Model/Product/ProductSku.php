<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

use InvalidArgumentException;

final class ProductSku
{
    /** @var string */
    private $value;

    public static function fromString(string $value): ProductSku
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

    public function equals(ProductSku $sku): bool
    {
        return $this->value === $sku->value;
    }

    private function __construct(string $value)
    {
        $this->value = $value;
    }
}
