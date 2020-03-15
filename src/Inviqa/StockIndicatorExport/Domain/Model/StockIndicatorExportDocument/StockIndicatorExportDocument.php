<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;

use ArrayIterator;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Iterator;
use IteratorAggregate;

/** @implements IteratorAggregate<StockIndicatorExportDocumentEntry> */
final class StockIndicatorExportDocument implements IteratorAggregate
{
    /** @var StockIndicatorExportDocumentId */
    private $documentId;

    /** @var StockIndicatorExportDocumentEntry[] */
    private $entries;

    /**
     * @param StockIndicatorExportDocumentId      $documentId
     * @param StockIndicatorExportDocumentEntry[] $entries
     *
     * @return StockIndicatorExportDocument
     */
    public static function fromDocumentIdAndEntries(
        StockIndicatorExportDocumentId $documentId,
        array $entries
    ): StockIndicatorExportDocument {
        return new self($documentId, $entries);
    }

    public static function fromDocumentId(StockIndicatorExportDocumentId $documentId): StockIndicatorExportDocument
    {
        return self::fromDocumentIdAndEntries($documentId, []);
    }

    public function documentId(): StockIndicatorExportDocumentId
    {
        return $this->documentId;
    }

    /** @return StockIndicatorExportDocumentEntry[] */
    public function entries(): array
    {
        return $this->entries;
    }

    public function addEntry(StockIndicatorExportDocumentEntry $entry): void
    {
        $this->entries[] = $entry;
    }

    public function hasEntryWithSku(ProductSku $productSku): bool
    {
        foreach ($this->entries as $entry) {
            if ($entry->sku()->equals($productSku)) {
                return true;
            }
        }

        return false;
    }

    /** @return Iterator|StockIndicatorExportDocumentEntry[] */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->entries);
    }

    /**
     * @param StockIndicatorExportDocumentId      $id
     * @param StockIndicatorExportDocumentEntry[] $entries
     */
    private function __construct(StockIndicatorExportDocumentId $id, array $entries)
    {
        $this->documentId = $id;
        $this->entries = $entries;
    }
}
