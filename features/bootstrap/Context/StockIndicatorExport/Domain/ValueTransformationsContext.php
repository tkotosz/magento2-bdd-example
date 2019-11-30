<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Domain;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;

class ValueTransformationsContext implements Context
{
    /**
     * @Transform
     */
    public function transformToSku(string $sku): Sku
    {
        return Sku::fromString($sku);
    }

    /**
     * @Transform
     */
    public function transformToStock(string $stock): Stock
    {
        return Stock::fromInt(intval($stock));
    }

    /**
     * @Transform
     */
    public function transformToStockIndicator(string $stockIndicator): StockIndicator
    {
        return StockIndicator::fromString(strtoupper($stockIndicator));
    }
}
