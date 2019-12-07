<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;
use Inviqa\StockIndicatorExport\Domain\Repository\ProductRepository;

final class InMemoryProductRepository implements ProductRepository
{
    /** @var Product[] */
    private $products = [];

    public function findBySku(ProductSku $productSku): Product
    {
        if (!array_key_exists($productSku->toString(), $this->products)) {
            throw ProductNotFoundException::fromSku($productSku);
        }

        return $this->products[$productSku->toString()];
    }

    public function findBySkuList(ProductSkuList $productSkuList): ProductList
    {
        $products = [];

        foreach ($this->products as $product) {
            if ($productSkuList->has($product->sku())) {
                $products[] = $product;
            }
        }

        return ProductList::fromProducts($products);
    }

    public function findAll(): ProductList
    {
        return ProductList::fromProducts($this->products);
    }

    public function add(Product $product): void
    {
        $this->products[$product->sku()->toString()] = $product;
    }

    public function clear(): void
    {
        $this->products = [];
    }

    public function remove(ProductSku $productSku): void
    {
        unset($this->products[$productSku->toString()]);
    }
}
