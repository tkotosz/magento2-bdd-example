<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model;

use InvalidArgumentException;

final class StockIndicator
{
    private const TYPE_RED = 'RED';
    private const TYPE_YELLOW = 'YELLOW';
    private const TYPE_GREEN = 'GREEN';

    private const LOW_STOCK_THRESHOLD = 10;

    /** @var string */
    private $value;

    public static function fromString(string $value): StockIndicator
    {
        if (!in_array($value, [self::TYPE_RED, self::TYPE_YELLOW, self::TYPE_GREEN], true)) {
            throw new InvalidArgumentException(
                sprintf('Stock indicator can only be RED, YELLOW or GREEN, but "%s" given', $value)
            );
        }

        return new self($value);
    }

    public static function red(): StockIndicator
    {
        return new self(self::TYPE_RED);
    }

    public static function yellow(): StockIndicator
    {
        return new self(self::TYPE_YELLOW);
    }

    public static function green(): StockIndicator
    {
        return new self(self::TYPE_GREEN);
    }

    public static function fromProduct(Product $product): StockIndicator
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
