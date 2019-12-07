<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\Product;

final class Product
{
    /** @var ProductSku */
    private $sku;

    /** @var ProductStock */
    private $stock;

    public static function fromSku(ProductSku $sku): Product
    {
        return new self($sku, ProductStock::outOfStock());
    }

    public static function fromSkuAndStock(ProductSku $sku, ProductStock $stock): Product
    {
        return new self($sku, $stock);
    }

    public function stock(): ProductStock
    {
        return $this->stock;
    }

    public function sku(): ProductSku
    {
        return $this->sku;
    }

    private function __construct(ProductSku $sku, ProductStock $stock)
    {
        $this->sku = $sku;
        $this->stock = $stock;
    }
}
