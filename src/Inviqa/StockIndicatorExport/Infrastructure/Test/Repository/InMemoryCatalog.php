<?php

namespace Inviqa\StockIndicatorExport\Infrastructure\Test\Repository;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use Inviqa\StockIndicatorExport\Domain\Repository\Catalog;

class InMemoryCatalog implements Catalog
{
    /** @var Product[] */
    private $products = [];

    /**
     * @inheritDoc
     */
    public function findBySku(Sku $sku): Product
    {
        if (!array_key_exists($sku->toString(), $this->products)) {
            throw ProductNotFoundException::fromSku($sku);
        }

        return $this->products[$sku->toString()];
    }

    /**
     * @inheritDoc
     */
    public function findBySkuList(SkuList $skuList): ProductList
    {
        $products = [];

        foreach ($skuList as $sku) {
            if (!array_key_exists($sku->toString(), $this->products)) {
                throw ProductNotFoundException::fromSku($sku);
            }

            $products[] = $this->products[$sku->toString()];
        }

        return ProductList::fromProducts($products);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ProductList
    {
        return ProductList::fromProducts($this->products);
    }

    public function add(Product $product): void
    {
        $this->products[$product->sku()->toString()] = $product;
    }

    public function remove(Sku $sku): void
    {
        unset($this->products[$sku->toString()]);
    }

    public function clear(): void
    {
        $this->products = [];
    }
}
