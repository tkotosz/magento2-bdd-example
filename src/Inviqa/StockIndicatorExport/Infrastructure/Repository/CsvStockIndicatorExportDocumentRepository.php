<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\Repository;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;
use Inviqa\StockIndicatorExport\Domain\Exception\StockIndicatorExportDocumentSaveFailedException;

final class CsvStockIndicatorExportDocumentRepository implements StockIndicatorExportDocumentRepository
{
    private const EXPORT_DIRECTORY = 'export/stock_indicator_export';

    /** @var Filesystem */
    private $filesystem;

    /** @var Csv */
    private $csvHandler;

    public function __construct(Filesystem $filesystem, Csv $csvHandler)
    {
        $this->filesystem = $filesystem;
        $this->csvHandler = $csvHandler;
    }

    public function save(StockIndicatorExportDocument $document): void
    {
        try {
            $directoryWriter = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $directoryWriter->create(self::EXPORT_DIRECTORY);

            $fileName = sprintf('stock_indicators_%s.csv', $document->documentId()->toString());
            $path = $directoryWriter->getAbsolutePath(self::EXPORT_DIRECTORY) . '/' . $fileName;

            $data = [];
            foreach ($document as $entry) {
                $data[] = [$entry->sku()->toString(), $entry->stockIndicator()->toString()];
            }

            $this->csvHandler->appendData($path, $data);
        } catch (FileSystemException $exception) {
            throw StockIndicatorExportDocumentSaveFailedException::fromDocumentIdAndPreviousError(
                $document->documentId(),
                $exception
            );
        }
    }
}
