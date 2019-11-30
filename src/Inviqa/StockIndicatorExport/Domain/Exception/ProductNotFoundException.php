<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Exception;

use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use RuntimeException;
use Throwable;

final class ProductNotFoundException extends RuntimeException
{
    public static function fromSku(Sku $sku): ProductNotFoundException
    {
        return new self(sprintf('Product \'%s\' does not exists', $sku->toString()));
    }

    public static function fromSkuAndPreviousError(Sku $sku, Throwable $previous): ProductNotFoundException
    {
        return new self(sprintf('Product \'%s\' does not exists', $sku->toString()), 0, $previous);
    }
}
