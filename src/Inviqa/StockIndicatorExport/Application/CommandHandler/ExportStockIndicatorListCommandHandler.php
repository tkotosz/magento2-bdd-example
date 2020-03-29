<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Application\CommandHandler;

use Inviqa\StockIndicatorExport\Application\Command\ExportStockIndicatorListCommand;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Exception\StockIndicatorExportDocumentSaveFailedException;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Repository\ProductRepository;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;

final class ExportStockIndicatorListCommandHandler
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
     * @param ExportStockIndicatorListCommand $command
     *
     * @throws ProductNotFoundException
     * @throws StockIndicatorExportDocumentSaveFailedException
     */
    public function handle(ExportStockIndicatorListCommand $command): void
    {
        $productList = $this->productRepository->findBySkuList($command->productSkuList());

        foreach ($command->productSkuList() as $productSku) {
            if (!$productList->has($productSku)) {
                throw ProductNotFoundException::fromSku($productSku);
            }
        }

        $document = StockIndicatorExportDocument::fromDocumentId($command->documentId());

        foreach ($productList as $product) {
            $document->addEntry(StockIndicatorExportDocumentEntry::fromProduct($product));
        }

        $this->stockIndicatorExportDocumentRepository->save($document);
    }
}
