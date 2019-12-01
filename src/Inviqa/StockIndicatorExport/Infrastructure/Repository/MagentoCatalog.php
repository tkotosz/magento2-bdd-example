<?php

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

class MagentoCatalog implements Catalog
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

    /**
     * @inheritDoc
     */
    public function findBySku(Sku $sku): Product
    {
        $this->searchCriteriaBuilder->addFilter('sku', $sku->toString(), 'eq');
        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        if ($stockItemSearchResult->getTotalCount() === 0) {
            throw ProductNotFoundException::fromSku($sku);
        }

        $items = $stockItemSearchResult->getItems();
        $stockItem = array_shift($items);

        return Product::fromSkuAndStock(
            Sku::fromString($stockItem->getSku()),
            Stock::fromInt((int) $stockItem->getQuantity())
        );
    }

    /**
     * @inheritDoc
     */
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
                Sku::fromString($stockItem->getSku()),
                Stock::fromInt((int) $stockItem->getQuantity())
            );
        }

        // interface says we need to throw an exception when a product is missing
        // probably wrong requirement or should not be handled here
        // or we should throw an exception with all missing sku - TBC
        $missingSkus = array_diff($skusRequested, $skusFound);
        if ($missingSkus !== []) {
            throw ProductNotFoundException::fromSku(Sku::fromString(array_shift($missingSkus)));
        }

        return ProductList::fromProducts($products);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ProductList
    {
        $products = [];

        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            $products[] =  Product::fromSkuAndStock(
                Sku::fromString($stockItem->getSku()),
                Stock::fromInt((int) $stockItem->getQuantity())
            );
        }

        return ProductList::fromProducts($products);
    }
}
