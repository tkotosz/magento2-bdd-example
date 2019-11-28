<?php

namespace Inviqa\StockIndicatorExport\Domain\Model;

use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;

final class Product
{
    /** @var Sku */
    private $sku;

    /** @var Stock */
    private $stock;

    public static function fromSkuAndStock(Sku $sku, Stock $stock): Product
    {
        return new self($sku, $stock);
    }

    public function stock(): Stock
    {
        return $this->stock;
    }

    public function sku(): Sku
    {
        return $this->sku;
    }

    private function __construct(Sku $sku, Stock $stock)
    {
        $this->sku = $sku;
        $this->stock = $stock;
    }
}
