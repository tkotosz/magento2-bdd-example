<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use InvalidArgumentException;
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
use Psr\Log\LoggerInterface;

final class MagentoApiBasedProductRepository implements ProductRepository
{
    /** @var ProductRepositoryInterface */
    private $magentoProductRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var SourceItemRepositoryInterface */
    private $sourceItemRepository;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ProductRepositoryInterface $magentoProductRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository,
        LoggerInterface $logger
    ) {
        $this->magentoProductRepository = $magentoProductRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->logger = $logger;
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

        try {
            return $this->transformToProduct($stockItem);
        } catch (InvalidArgumentException $e) {
            $this->logTransformationError($stockItem, $e);
            throw ProductNotFoundException::fromSku($productSku);
        }
    }

    public function findBySkuList(ProductSkuList $productSkuList): ProductList
    {
        $products = [];

        $this->searchCriteriaBuilder->addFilter('sku', $productSkuList->toStringArray(), 'in');
        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            try {
                $products[] = $this->transformToProduct($stockItem);
            } catch (InvalidArgumentException $e) {
                $this->logTransformationError($stockItem, $e);
            }
        }

        return ProductList::fromProducts($products);
    }

    public function findAll(): ProductList
    {
        $products = [];

        $stockItemSearchResult = $this->sourceItemRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($stockItemSearchResult->getItems() as $stockItem) {
            try {
                $products[] = $this->transformToProduct($stockItem);
            } catch (InvalidArgumentException $e) {
                $this->logTransformationError($stockItem, $e);
            }
        }

        return ProductList::fromProducts($products);
    }

    /**
     * @param SourceItemInterface $stockItem
     *
     * @return Product
     *
     * @throws InvalidArgumentException
     */
    private function transformToProduct(SourceItemInterface $stockItem): Product
    {
        return Product::fromSkuAndStock(
            ProductSku::fromString($stockItem->getSku() ?? ''),
            ProductStock::fromInt((int) ($stockItem->getQuantity() ?? 0))
        );
    }

    private function logTransformationError(SourceItemInterface $stockItem, InvalidArgumentException $exception): void
    {
        $this->logger->notice(
            'An error occurred during stock item to product transformation',
            [
                'exception' => $exception,
                'stock_item_data' => [
                    'sku' => $stockItem->getSku(),
                    'quantity' => $stockItem->getQuantity()
                ]
            ]
        );
    }
}
