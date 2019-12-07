<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductStock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicator;

class TransformationsContext implements Context
{
    /**
     * @Transform
     */
    public function transformStringToProductSku(string $productSku): ProductSku
    {
        return ProductSku::fromString($productSku);
    }

    /**
     * @Transform
     */
    public function transformStringToProductStock(string $productStock): ProductStock
    {
        return ProductStock::fromInt(intval($productStock));
    }

    /**
     * @Transform
     */
    public function transformStringToStockIndicator(string $stockIndicator): StockIndicator
    {
        return StockIndicator::fromString(strtoupper($stockIndicator));
    }

    /**
     * @Transform
     */
    public function transformTableToProductSkuList(TableNode $tableNode): ProductSkuList
    {
        $skus = [];

        foreach ($tableNode->getColumnsHash() as $row) {
            $skus[] = $this->transformStringToProductSku($row['sku']);
        }

        return ProductSkuList::fromSkus($skus);
    }
}
