<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use Inviqa\StockIndicatorExport\Domain\Repository\Catalog;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;

final class MagentoCatalog implements Catalog
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

    public function findBySku(Sku $sku): Product
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $sku->toString(), 'eq')
            ->create();

        $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        $stockItem = array_shift($items);

        if ($stockItem === null) {
            throw ProductNotFoundException::fromSku($sku);
        }

        return Product::fromSkuAndStock(
            Sku::fromString($stockItem->getSku() ?? ''),
            Stock::fromInt((int) ($stockItem->getQuantity() ?? 0))
        );
    }

    public function findBySkuList(SkuList $skuList): ProductList
    {
        $products = [];
        $skusRequested = $skuList->toStrings();
        $skusFound = [];

        $this->searchCriteriaBuilder->addFilter('sku', $skusRequested, 'in');
        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            $skusFound[] = $stockItem->getSku();
            $products[] =  Product::fromSkuAndStock(
                Sku::fromString($stockItem->getSku() ?? ''),
                Stock::fromInt((int) ($stockItem->getQuantity() ?? 0))
            );
        }

        // interface says we need to throw an exception when a product is missing
        // probably wrong requirement or should not be handled here
        // or we should throw an exception with all missing sku - TBC
        $missingSkus = array_diff($skusRequested, $skusFound);
        if ($missingSkus !== []) {
            throw ProductNotFoundException::fromSku(Sku::fromString((string) array_shift($missingSkus)));
        }

        return ProductList::fromProducts($products);
    }

    public function findAll(): ProductList
    {
        $products = [];

        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            $products[] =  Product::fromSkuAndStock(
                Sku::fromString($stockItem->getSku() ?? ''),
                Stock::fromInt((int) ($stockItem->getQuantity() ?? 0))
            );
        }

        return ProductList::fromProducts($products);
    }
}
