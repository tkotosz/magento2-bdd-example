<?php

namespace Inviqa\StockIndicatorExport\Domain\Service;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
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
        $product = $this->catalog->findBySku($sku);

        $entry = DocumentEntry::fromSkuAndStockIndicator(
            $product->sku(),
            StockIndicator::fromProductStock($product->stock())
        );

        $document = StockIndicatorExportDocument::fromEntries([$entry]);

        $this->exportDocumentRepository->save($document);
    }

    /**
     * @param SkuList $skuList
     *
     * @return void
     * @throws ProductNotFoundException
     */
    public function exportList(SkuList $skuList): void
    {
        $entries = [];
        foreach ($this->catalog->findBySkuList($skuList) as $product) {
            $entries[] = DocumentEntry::fromSkuAndStockIndicator(
                $product->sku(),
                StockIndicator::fromProductStock($product->stock())
            );
        }

        $document = StockIndicatorExportDocument::fromEntries($entries);

        $this->exportDocumentRepository->save($document);
    }

    public function exportAll(): void
    {
        $entries = [];
        foreach ($this->catalog->findAll() as $product) {
            $entries[] = DocumentEntry::fromSkuAndStockIndicator(
                $product->sku(),
                StockIndicator::fromProductStock($product->stock())
            );
        }

        $document = StockIndicatorExportDocument::fromEntries($entries);

        $this->exportDocumentRepository->save($document);
    }
}
