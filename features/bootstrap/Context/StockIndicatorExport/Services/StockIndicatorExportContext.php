<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Services;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use PHPUnit\Framework\Assert;

class StockIndicatorExportContext implements Context
{
    /** @var Product|null */
    private $product = null;

    /** @var StockIndicatorExportDocument|null */
    private $stockIndicatorExportDocument = null;

    /** @var int */
    private $expectedNumberOfDocumentEntries = 0;

    /** @var ProductNotFoundException|null */
    private $exportError = null;

    /** @var StockIndicator|null */
    private $inspectedStockIndicator = null;

    /**
     * @Given there is a product in the catalog with sku :sku
     */
    public function thereIsAProductInTheCatalogWithSku(Sku $sku)
    {
        throw new PendingException('TODO');
    }

    /**
     * @Given there is a product in the catalog that has a stock level of :stock
     */
    public function thereIsAProductInTheCatalogThatHasAStockLevelOf(Stock $stock)
    {
        throw new PendingException('TODO');
    }

    /**
     * @Given the product with sku :sku does not exists in the catalog
     */
    public function theProductWithSkuDoesNotExistsInTheCatalog(Sku $sku)
    {
        throw new PendingException('TODO');
    }

    /**
     * @Given there are no other products in the catalog
     */
    public function thereAreNoOtherProductsInTheCatalog()
    {
        throw new PendingException('TODO');
    }

    /**
     * @Given the stock indicator export document has been generated for this product
     */
    public function theStockIndicatorExportDocumentHasBeenGeneratedForThisProduct()
    {
        throw new PendingException('TODO');
    }

    /**
     * @When the user runs the stock indicator export for this product
     */
    public function theUserRunsTheStockIndicatorExportForThisProduct()
    {
        throw new PendingException('TODO');
    }

    /**
     * @When the user runs the stock indicator export for :sku
     */
    public function theUserRunsTheStockIndicatorExportForASku(Sku $sku)
    {
        throw new PendingException('TODO');
    }

    /**
     * @When the user checks the stock indicator for this product in the document
     */
    public function theUserChecksTheStockIndicatorForThisProductInTheDocument()
    {
        throw new PendingException('TODO');
    }

    /**
     * @When the user runs the stock indicator export for :firstSku and :secondSku
     */
    public function theUserRunsTheStockIndicatorExportForAListOfSkus(Sku $firstSku, Sku $secondSku)
    {
        throw new PendingException('TODO');
    }

    /**
     * @When the user runs the stock indicator export for the complete catalog
     */
    public function theUserRunsTheStockIndicatorExportForTheCompleteProduct()
    {
        throw new PendingException('TODO');
    }

    /**
     * @Then a stock indicator export document is generated
     */
    public function aStockIndicatorExportDocumentIsGenerated()
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);
    }

    /**
     * @Then the document contains an entry for :sku
     */
    public function theDocumentContainsAnEntryFor(Sku $sku)
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);

        $foundEntry = null;
        foreach ($this->stockIndicatorExportDocument as $entry) {
            if ($entry->sku()->equals($sku)) {
                $foundEntry = $entry;
            }
        }

        Assert::assertNotNull($foundEntry);
        $this->expectedNumberOfDocumentEntries++;
    }

    /**
     * @Then the document does not have any further entries
     */
    public function theDocumentDoesNotHaveAnyFurtherEntries()
    {
        Assert::assertCount($this->expectedNumberOfDocumentEntries, $this->stockIndicatorExportDocument);
    }

    /**
     * @Then a product not found error for :sku is shown
     */
    public function aProductNotFoundErrorForSkuIsShown(Sku $sku)
    {
        Assert::assertEquals(ProductNotFoundException::fromSku($sku), $this->exportError);
    }

    /**
     * @Then a stock indicator export document is not generated
     */
    public function aStockIndicatorExportDocumentIsNotGenerated()
    {
        Assert::assertNull($this->stockIndicatorExportDocument);
    }

    /**
     * @Then the user sees a :stockIndicator stock indicator
     */
    public function theUserSeesAStockIndicator(StockIndicator $stockIndicator)
    {
        Assert::assertEquals($stockIndicator, $this->inspectedStockIndicator);
    }
}
