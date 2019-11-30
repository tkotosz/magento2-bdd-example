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

class MagentoCatalog implements Catalog
{
    /** @var ProductRepositoryInterface */
    private $magentoProductRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    public function __construct(
        ProductRepositoryInterface $magentoProductRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->magentoProductRepository = $magentoProductRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function findBySku(Sku $sku): Product
    {
        try {
            $magentoProduct = $this->magentoProductRepository->get($sku->toString());
        } catch (NoSuchEntityException $e) {
            throw ProductNotFoundException::fromSku($sku);
        }

        return Product::fromSkuAndStock(
            Sku::fromString($magentoProduct->getSku()),
            Stock::fromInt((int) $magentoProduct->getExtensionAttributes()->getStockItem()->getQty())
        );
    }

    /**
     * @inheritDoc
     */
    public function findBySkuList(SkuList $skuList): ProductList
    {
        $products = [];

        // TODO
        foreach ($skuList as $sku) {
            $products[] = $this->findBySku($sku);
        }

        return ProductList::fromProducts($products);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ProductList
    {
        $products = [];

        // TODO
        $this->searchCriteriaBuilder->addFilter('sku', ['INVIQA-001', 'INVIQA-002', 'INVIQA-003'], 'in');

        $magentoProducts = $this->magentoProductRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($magentoProducts->getItems() as $magentoProduct) {

            // REMOVE THIS BEGIN !!!
            $magentoProduct = $this->magentoProductRepository->get($magentoProduct->getSku()); // reload to get stock :D
            // REMOVE THIS END !!!

            $stockItem = $magentoProduct->getExtensionAttributes()->getStockItem();
            $products[] =  Product::fromSkuAndStock(
                Sku::fromString($magentoProduct->getSku()),
                Stock::fromInt((int) $magentoProduct->getExtensionAttributes()->getStockItem()->getQty())
            );
        }

        return ProductList::fromProducts($products);
    }
}
