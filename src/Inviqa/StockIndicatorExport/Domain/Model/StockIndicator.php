<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model;

use InvalidArgumentException;

final class StockIndicator
{
    private const LOW_STOCK_THRESHOLD = 10;

    /** @var string */
    private $value;

    public static function fromString(string $value): StockIndicator
    {
        if (!in_array($value, ['RED', 'YELLOW', 'GREEN'], true)) {
            throw new InvalidArgumentException(
                'Stock indicator can only be RED, YELLOW or GREEN, but ' . $value . ' given'
            );
        }

        return new self($value);
    }

    public static function red(): StockIndicator
    {
        return new self('RED');
    }

    public static function yellow(): StockIndicator
    {
        return new self('YELLOW');
    }

    public static function green(): StockIndicator
    {
        return new self('GREEN');
    }

    public static function for(Product $product): StockIndicator
    {
        if ($product->stock()->toInt() === 0) {
            return self::red();
        }

        if ($product->stock()->toInt() <= self::LOW_STOCK_THRESHOLD) {
            return self::yellow();
        }

        return self::green();
    }

    public function sameAs(StockIndicator $red): bool
    {
        return $this->value === $red->value;
    }

    private function __construct(string $value)
    {
        $this->value = $value;
    }
}
