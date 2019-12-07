<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Application\ExportStockIndicator;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Exception\StockIndicatorExportDocumentSaveFailedException;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Repository\ProductRepository;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

final class ExportStockIndicatorCommandHandler
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
     * @param ExportStockIndicatorCommand $command
     *
     * @throws ProductNotFoundException
     * @throws StockIndicatorExportDocumentSaveFailedException
     */
    public function handle(ExportStockIndicatorCommand $command): void
    {
        $product = $this->productRepository->findBySku($command->productSku());

        $document = StockIndicatorExportDocument::fromDocumentIdAndEntries(
            $command->documentId(),
            [StockIndicatorExportDocumentEntry::fromProduct($product)]
        );

        $this->stockIndicatorExportDocumentRepository->save($document);
    }
}
