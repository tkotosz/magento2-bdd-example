<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Domain;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use PHPUnit\Framework\Assert;

class StockIndicatorExportContext implements Context
{
    /** @var Product[] */
    private $catalog = [];

    /** @var Product|null */
    private $product = null;

    /** @var StockIndicatorExportDocument */
    private $stockIndicatorExportDocument;

    /** @var int */
    private $expectedNumberOfEntries = 0;

    /**
     * @Given /^there is a product with sku ([^ ]*) in the catalog that has a stock level of (\d+)$/
     */
    public function thereIsAProductWithSkuInTheCatalogThatHasAStockLevelOf(string $sku, int $stock)
    {
        $product = Product::fromSkuAndStock(Sku::fromString($sku), Stock::fromInt($stock));
        $this->catalog[] = $product;
        $this->product = $product;
    }

    /**
     * @Given there are no other products in the catalog
     */
    public function thereAreNoOtherProductsInTheCatalog()
    {
        // no-op
    }

    /**
     * @When I export the stock indicator for that product
     */
    public function iExportTheStockIndicatorForThisProduct()
    {
        $stockIndicator = StockIndicator::fromProductStock($this->product->stock());
        $exportEntry = DocumentEntry::fromSkuAndStockIndicator($this->product->sku(), $stockIndicator);
        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries([$exportEntry]);
    }

    /**
     * @When I export the stock indicator for the whole catalog
     */
    public function iExportTheStockIndicatorForAllProducts()
    {
        foreach ($this->catalog as $product) {
            $stockIndicator = StockIndicator::fromProductStock($product->stock());
            $exportEntries[] = DocumentEntry::fromSkuAndStockIndicator($product->sku(), $stockIndicator);
        }

        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries($exportEntries);
    }

    /**
     * @Then I should get a stock indicator export document
     */
    public function iShouldGetAStockIndicatorExportDocument()
    {
        Assert::assertInstanceOf(StockIndicatorExportDocument::class, $this->stockIndicatorExportDocument);
    }

    /**
     * @Then /^the document should contain an entry for ([^ ]*) with a red stock indicator$/
     */
    public function theDocumentShouldContainAnEntryForProductWithARedStockIndicator(string $sku)
    {
        $productDocumentEntry = null;

        foreach ($this->stockIndicatorExportDocument as $documentEntry) {
            if ($documentEntry->sku()->toString() === $sku) {
                $productDocumentEntry = $documentEntry;
            }
        }

        Assert::assertNotNull($productDocumentEntry);
        Assert::assertEquals(StockIndicator::red(), $productDocumentEntry->stockIndicator());
        $this->expectedNumberOfEntries++;
    }

    /**
     * @Then /^the document should contain an entry for ([^ ]*) with a yellow stock indicator$/
     */
    public function theDocumentShouldContainAnEntryForINVIQAWithAYellowStockIndicator(string $sku)
    {
        $productDocumentEntry = null;

        foreach ($this->stockIndicatorExportDocument as $documentEntry) {
            if ($documentEntry->sku()->toString() === $sku) {
                $productDocumentEntry = $documentEntry;
            }
        }

        Assert::assertNotNull($productDocumentEntry);
        Assert::assertEquals(StockIndicator::yellow(), $productDocumentEntry->stockIndicator());
        $this->expectedNumberOfEntries++;
    }

    /**
     * @Then /^the document should contain an entry for ([^ ]*) with a green stock indicator$/
     */
    public function theDocumentShouldContainAnEntryForINVIQAWithAGreenStockIndicator(string $sku)
    {
        $productDocumentEntry = null;

        foreach ($this->stockIndicatorExportDocument as $documentEntry) {
            if ($documentEntry->sku()->toString() === $sku) {
                $productDocumentEntry = $documentEntry;
            }
        }

        Assert::assertNotNull($productDocumentEntry);
        Assert::assertEquals(StockIndicator::green(), $productDocumentEntry->stockIndicator());
        $this->expectedNumberOfEntries++;
    }

    /**
     * @Given /^the document should not have any further entries$/
     */
    public function theDocumentShouldNotHaveAnyFurtherEntries()
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);
        Assert::assertCount($this->expectedNumberOfEntries, $this->stockIndicatorExportDocument);
    }
}
