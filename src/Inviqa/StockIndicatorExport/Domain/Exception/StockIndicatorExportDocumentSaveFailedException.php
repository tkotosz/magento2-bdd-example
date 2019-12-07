<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Exception;

use Exception;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;
use RuntimeException;

final class StockIndicatorExportDocumentSaveFailedException extends RuntimeException
{
    public static function fromDocumentIdAndPreviousError(
        StockIndicatorExportDocumentId $documentId,
        Exception $previousError
    ): StockIndicatorExportDocumentSaveFailedException {
        $message = sprintf(
            'An error occurred while saving the Stock Indicator Document with id "%s". Error was: %s',
            $documentId->toString(),
            $previousError->getMessage()
        );

        return new self($message, 0, $previousError);
    }
}
