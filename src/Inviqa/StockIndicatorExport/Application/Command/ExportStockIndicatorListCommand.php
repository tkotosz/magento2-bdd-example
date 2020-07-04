<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Application\Command;

use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;

final class ExportStockIndicatorListCommand
{
    /** @var string */
    private $documentId;

    /** @var string[] */
    private $productSkuList;

    /**
     * @param string   $documentId
     * @param string[] $productSkuList
     */
    public function __construct(string $documentId, array $productSkuList)
    {
        // TODO another test
        $this->documentId = $documentId;
        $this->productSkuList = $productSkuList;
    }

    public function documentId(): StockIndicatorExportDocumentId
    {
        return StockIndicatorExportDocumentId::fromString($this->documentId);
    }

    public function productSkuList(): ProductSkuList
    {
        $skuList = [];

        foreach ($this->productSkuList as $productSku) {
            $skuList[] = ProductSku::fromString($productSku);
        }

        return ProductSkuList::fromSkus($skuList);
    }
}
