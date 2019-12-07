<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Application\ExportAllStockIndicator;

use Inviqa\StockIndicatorExport\Domain\Exception\StockIndicatorExportDocumentSaveFailedException;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Repository\ProductRepository;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

final class ExportAllStockIndicatorCommandHandler
{
    /** @var ProductRepository */
    private $productRepository;

    /** @var StockIndicatorExportDocumentRepository */
    private $stockIndicatorExportDocumentRepository;

    public function __construct(
        ProductRepository $productRepository,
        StockIndicatorExportDocumentRepository $stockIndicatorExportDocumentRepository
    ) {
        $this->productRepository = $productRepository;
        $this->stockIndicatorExportDocumentRepository = $stockIndicatorExportDocumentRepository;
    }

    /**
     * @param ExportAllStockIndicatorCommand $command
     *
     * @throws StockIndicatorExportDocumentSaveFailedException
     */
    public function handle(ExportAllStockIndicatorCommand $command): void
    {
        $productList = $this->productRepository->findAll();

        $document = StockIndicatorExportDocument::fromDocumentId($command->documentId());

        foreach ($productList as $product) {
            $document->addEntry(StockIndicatorExportDocumentEntry::fromProduct($product));
        }

        $this->stockIndicatorExportDocumentRepository->save($document);
    }
}
