<?php

namespace Inviqa\StockIndicatorExport\Domain\Repository;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;

interface Catalog
{
    /**
     * @param Sku $sku
     *
     * @return Product
     * @throws ProductNotFoundException
     */
    public function findBySku(Sku $sku): Product;

    /**
     * @param Sku $sku
     *
     * @return ProductList
     * @throws ProductNotFoundException
     */
    public function findBySkuList(SkuList $skuList): ProductList;

    /**
     * @return ProductList
     */
    public function findAll(): ProductList;
}
