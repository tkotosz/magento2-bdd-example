<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Repository;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;

interface ProductRepository
{
    /**
     * @param ProductSku $productSku
     *
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    public function findBySku(ProductSku $productSku): Product;

    public function findBySkuList(ProductSkuList $productSkuList): ProductList;

    public function findAll(): ProductList;
}
