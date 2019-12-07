<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductStock;
use Inviqa\StockIndicatorExport\Domain\Repository\ProductRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;

final class MagentoApiBasedProductRepository implements ProductRepository
{
    /** @var ProductRepositoryInterface */
    private $magentoProductRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var SourceItemRepositoryInterface */
    private $sourceItemRepository;

    public function __construct(
        ProductRepositoryInterface $magentoProductRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository
    ) {
        $this->magentoProductRepository = $magentoProductRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
    }

    public function findBySku(ProductSku $productSku): Product
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $productSku->toString(), 'eq')
            ->create();

        $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        $stockItem = array_shift($items);

        if ($stockItem === null) {
            throw ProductNotFoundException::fromSku($productSku);
        }

        return $this->transformToProduct($stockItem);
    }

    public function findBySkuList(ProductSkuList $productSkuList): ProductList
    {
        $products = [];

        $this->searchCriteriaBuilder->addFilter('sku', $productSkuList->toStringArray(), 'in');
        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            $products[] = $this->transformToProduct($stockItem);
        }

        return ProductList::fromProducts($products);
    }

    public function findAll(): ProductList
    {
        $products = [];

        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            $products[] = $this->transformToProduct($stockItem);
        }

        return ProductList::fromProducts($products);
    }

    private function transformToProduct(SourceItemInterface $stockItem): Product
    {
        return Product::fromSkuAndStock(
            ProductSku::fromString($stockItem->getSku() ?? ''),
            ProductStock::fromInt((int) ($stockItem->getQuantity() ?? 0))
        );
    }
}
