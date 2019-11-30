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
use Inviqa\StockIndicatorExport\Domain\Service\StockIndicatorExporter;
use Inviqa\StockIndicatorExport\Infrastructure\Test\Repository\InMemoryCatalog;
use Inviqa\StockIndicatorExport\Infrastructure\Test\Repository\InMemoryStockIndicatorExportDocumentRepository;
use PHPUnit\Framework\Assert;

class StockIndicatorExportContext implements Context
{
    /** @var InMemoryCatalog */
    private $catalog;

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

    /** @var StockIndicatorExporter */
    private $stockIndicatorExporter;

    /** @var InMemoryStockIndicatorExportDocumentRepository */
    private $stockIndicatorExportDocumentRepository;

    public function __construct(
        InMemoryCatalog $catalog,
        StockIndicatorExporter $stockIndicatorExporter,
        InMemoryStockIndicatorExportDocumentRepository $stockIndicatorExportDocumentRepository
    ) {
        $this->catalog = $catalog;
        $this->stockIndicatorExporter = $stockIndicatorExporter;
        $this->stockIndicatorExportDocumentRepository = $stockIndicatorExportDocumentRepository;
    }

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        $this->catalog->clear();
        $this->stockIndicatorExportDocumentRepository->clear();
    }

    /**
     * @Given there is a product in the catalog with sku :sku
     */
    public function thereIsAProductInTheCatalogWithSku(Sku $sku)
    {
        $product = Product::fromSku($sku);
        $this->catalog->add($product);
        $this->product = $product;
    }

    /**
     * @Given there is a product in the catalog that has a stock level of :stock
     */
    public function thereIsAProductInTheCatalogThatHasAStockLevelOf(Stock $stock)
    {
        $product = Product::fromSkuAndStock(Sku::fromString(uniqid()), $stock);
        $this->catalog->add($product);
        $this->product = $product;
    }

    /**
     * @Given the product with sku :sku does not exists in the catalog
     */
    public function theProductWithSkuDoesNotExistsInTheCatalog(Sku $sku)
    {
        $this->catalog->remove($sku);
    }

    /**
     * @Given there are no other products in the catalog
     */
    public function thereAreNoOtherProductsInTheCatalog()
    {
        // no-op
    }

    /**
     * @Given the stock indicator export document has been generated for this product
     */
    public function theStockIndicatorExportDocumentHasBeenGeneratedForThisProduct()
    {
        $this->theUserRunsTheStockIndicatorExportForASku($this->product->sku());
    }

    /**
     * @When the user runs the stock indicator export for this product
     */
    public function theUserRunsTheStockIndicatorExportForThisProduct()
    {
        $this->stockIndicatorExporter->export($this->product->sku());
        $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->getLast();
    }

    /**
     * @When the user runs the stock indicator export for :sku
     */
    public function theUserRunsTheStockIndicatorExportForASku(Sku $sku)
    {
        try {
            $this->stockIndicatorExporter->export($sku);
            $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->getLast();
        } catch (ProductNotFoundException $e) {
            $this->exportError = $e;
        }
    }

    /**
     * @When the user runs the stock indicator export for :firstSku and :secondSku
     */
    public function theUserRunsTheStockIndicatorExportForAListOfSkus(Sku $firstSku, Sku $secondSku)
    {
        try {
            $this->stockIndicatorExporter->exportList(SkuList::fromSkus([$firstSku, $secondSku]));
            $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->getLast();
        } catch (ProductNotFoundException $e) {
            $this->exportError = $e;
        }
    }

    /**
     * @When the user runs the stock indicator export for the complete catalog
     */
    public function theUserRunsTheStockIndicatorExportForTheCompleteCatalog()
    {
        $this->stockIndicatorExporter->exportAll();
        $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->getLast();
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
