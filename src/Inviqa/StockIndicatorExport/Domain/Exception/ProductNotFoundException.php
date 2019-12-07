<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Exception;

use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use RuntimeException;
use Throwable;

final class ProductNotFoundException extends RuntimeException
{
    public static function fromSku(ProductSku $sku): ProductNotFoundException
    {
        return new self(sprintf('Product \'%s\' does not exists', $sku->toString()));
    }

    public static function fromSkuAndPreviousError(ProductSku $sku, Throwable $previous): ProductNotFoundException
    {
        return new self(sprintf('Product \'%s\' does not exists', $sku->toString()), 0, $previous);
    }
}
