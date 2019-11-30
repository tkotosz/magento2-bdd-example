<?php

namespace Inviqa\StockIndicatorExport\Domain\Service;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Repository\Catalog;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

class StockIndicatorExporter
{
    /** @var Catalog */
    private $catalog;

    /** @var StockIndicatorExportDocumentRepository */
    private $exportDocumentRepository;

    public function __construct(
        Catalog $catalog,
        StockIndicatorExportDocumentRepository $exportDocumentRepository
    ) {
        $this->catalog = $catalog;
        $this->exportDocumentRepository = $exportDocumentRepository;
    }

    /**
     * @param Sku $sku
     *
     * @return void
     * @throws ProductNotFoundException
     */
    public function export(Sku $sku): void
    {
        $this->exportProducts(ProductList::fromProducts([$this->catalog->findBySku($sku)]));
    }

    /**
     * @param SkuList $skuList
     *
     * @return void
     * @throws ProductNotFoundException
     */
    public function exportList(SkuList $skuList): void
    {
        $this->exportProducts($this->catalog->findBySkuList($skuList));
    }

    public function exportAll(): void
    {
        $this->exportProducts($this->catalog->findAll());
    }

    private function exportProducts(ProductList $productList): void
    {
        $entries = [];

        foreach ($productList as $product) {
            $entries[] = DocumentEntry::fromSkuAndStockIndicator(
                $product->sku(),
                StockIndicator::fromProductStock($product->stock())
            );
        }

        $this->exportDocumentRepository->save(StockIndicatorExportDocument::fromEntries($entries));
    }
}
