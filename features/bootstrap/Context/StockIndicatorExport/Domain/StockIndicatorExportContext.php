<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Domain;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
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

    /** @var int */
    private $expectedNumberOfCatalogEntries = 0;

    /** @var StockIndicatorExportDocument|null */
    private $stockIndicatorExportDocument = null;

    /** @var int */
    private $expectedNumberOfDocumentEntries = 0;

    /** @var ProductNotFoundException|null */
    private $exportException = null;

    /** @var StockIndicator|null */
    private $inspectedStockIndicator = null;

    /**
     * @Transform
     */
    public function transformSku(string $sku): Sku
    {
        return Sku::fromString($sku);
    }

    /**
     * @Transform
     */
    public function transformStock(string $stock): Stock
    {
        return Stock::fromInt(intval($stock));
    }

    /**
     * @Transform
     */
    public function transformStockIndicator(string $type): StockIndicator
    {
        return StockIndicator::fromString(strtoupper($type));
    }

    /**
     * @Given there is a product in the catalog with sku :sku
     */
    public function thereIsAProductInTheCatalogWithSku(Sku $sku)
    {
        $product = Product::fromSku($sku);
        $this->catalog[$product->sku()->toString()] = $product;
        $this->product = $product;
        $this->expectedNumberOfCatalogEntries++;
    }

    /**
     * @When the user runs the stock indicator export for this product
     */
    public function theUserRunsTheStockIndicatorExportForThisProduct()
    {
        $entry = DocumentEntry::fromSkuAndStockIndicator(
            $this->product->sku(),
            StockIndicator::fromProductStock($this->product->stock())
        );
        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries([$entry]);
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
     * @Given the product with sku :sku does not exists in the catalog
     */
    public function theProductWithSkuDoesNotExistsInTheCatalog(Sku $sku)
    {
        unset($this->catalog[$sku->toString()]);
    }

    /**
     * @When the user runs the stock indicator export for :sku
     */
    public function theUserRunsTheStockIndicatorExportFor(Sku $sku)
    {
        $product = $this->catalog[$sku->toString()] ?? null;

        if ($product === null) {
            $this->exportException = ProductNotFoundException::fromSku($sku);
            return;
        }

        $entry = DocumentEntry::fromSkuAndStockIndicator(
            $product->sku(),
            StockIndicator::fromProductStock($product->stock())
        );
        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries([$entry]);
    }

    /**
     * @Then a :errorMessage error is shown
     */
    public function aErrorIsShown(string $errorMessage)
    {
        Assert::assertInstanceOf(ProductNotFoundException::class, $this->exportException);
        Assert::assertEquals($errorMessage, $this->exportException->getMessage());
    }

    /**
     * @Then a stock indicator export document is not generated
     */
    public function aStockIndicatorExportDocumentIsNotGenerated()
    {
        Assert::assertNull($this->stockIndicatorExportDocument);
    }

    /**
     * @Given there is a product in the catalog that has a stock level of :stock
     */
    public function thereIsAProductInTheCatalogThatHasAStockLevelOf(Stock $stock)
    {
        $product = Product::fromSkuAndStock(Sku::fromString(uniqid()), $stock);
        $this->catalog[$product->sku()->toString()] = $product;
        $this->product = $product;
        $this->expectedNumberOfCatalogEntries++;
    }

    /**
     * @Given the stock indicator export document has been generated for this product
     */
    public function theStockIndicatorExportDocumentHasBeenGeneratedForThisProduct()
    {
        $this->theUserRunsTheStockIndicatorExportFor($this->product->sku());
    }

    /**
     * @When the user checks the stock indicator for this product in the document
     */
    public function theUserChecksTheStockIndicatorForThisProductInTheDocument()
    {
        $this->inspectedStockIndicator = null;

        foreach ($this->stockIndicatorExportDocument as $entry) {
            if ($entry->sku()->equals($this->product->sku())) {
                $this->inspectedStockIndicator = $entry->stockIndicator();
                break;
            }
        }
    }

    /**
     * @Then the user sees a :stockIndicator stock indicator
     */
    public function theUserSeesAStockIndicator(StockIndicator $stockIndicator)
    {
        Assert::assertEquals($stockIndicator, $this->inspectedStockIndicator);
    }

    /**
     * @When the user runs the stock indicator export for :firstSku and :secondSku
     */
    public function theUserRunsTheStockIndicatorExportForAnd(Sku $firstSku, Sku $secondSku)
    {
        $firstProduct = $this->catalog[$firstSku->toString()] ?? null;
        $secondProduct = $this->catalog[$secondSku->toString()] ?? null;

        if ($firstProduct === null) {
            $this->exportException = ProductNotFoundException::fromSku($firstSku);
            return;
        }

        if ($secondProduct === null) {
            $this->exportException = ProductNotFoundException::fromSku($secondSku);
            return;
        }

        $entries = [];

        /** @var Product $product */
        foreach ([$firstProduct, $secondProduct] as $product) {
            $entries[] = DocumentEntry::fromSkuAndStockIndicator(
                $product->sku(),
                StockIndicator::fromProductStock($product->stock())
            );
        }

        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries($entries);
    }

    /**
     * @Given there are no other products in the catalog
     */
    public function thereAreNoOtherProductsInTheCatalog()
    {
        Assert::assertCount($this->expectedNumberOfCatalogEntries, $this->catalog);
    }

    /**
     * @When the user runs the stock indicator export for the complete catalog
     */
    public function theUserRunsTheStockIndicatorExportForTheCompleteProduct()
    {
        $entries = [];

        foreach ($this->catalog as $product) {
            $entries[] = DocumentEntry::fromSkuAndStockIndicator(
                $product->sku(),
                StockIndicator::fromProductStock($product->stock())
            );
        }

        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries($entries);
    }
}
