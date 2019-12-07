<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

final class StockIndicatorExportDocumentId
{
    /** @var string */
    private $id;

    public static function fromString(string $id): StockIndicatorExportDocumentId
    {
        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    private function __construct(string $id)
    {
        $this->id = $id;
    }
}
