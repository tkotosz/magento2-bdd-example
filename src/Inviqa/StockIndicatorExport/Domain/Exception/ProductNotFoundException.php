<?php

namespace Inviqa\StockIndicatorExport\Domain\Exception;

use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use RuntimeException;

class ProductNotFoundException extends RuntimeException
{
    public static function fromSku(Sku $sku): ProductNotFoundException
    {
        return new self(sprintf('Product \'%s\' does not exists', $sku->toString()));
    }
}
