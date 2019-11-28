<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Domain\Model;

use ArrayIterator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use IteratorAggregate;
use Traversable;

final class StockIndicatorExportDocument implements IteratorAggregate
{
    /** @var DocumentEntry[] */
    private $entries;

    /**
     * @param DocumentEntry[] $entries
     *
     * @return StockIndicatorExportDocument
     */
    public static function fromEntries(array $entries): StockIndicatorExportDocument
    {
        return new self($entries);
    }

    /**
     * @return Traversable|DocumentEntry[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->entries);
    }

    /**
     * @param DocumentEntry[] $entries
     */
    private function __construct(array $entries)
    {
        $this->entries = $entries;
    }
}
