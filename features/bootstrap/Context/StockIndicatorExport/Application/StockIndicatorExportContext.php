<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Application;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Application\ExportAllStockIndicator\ExportAllStockIndicatorCommand;
use Inviqa\StockIndicatorExport\Application\ExportAllStockIndicator\ExportAllStockIndicatorCommandHandler;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicator\ExportStockIndicatorCommand;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicator\ExportStockIndicatorCommandHandler;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicatorList\ExportStockIndicatorListCommand;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicatorList\ExportStockIndicatorListCommandHandler;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductSkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\ProductStock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\StockIndicatorExportDocumentId;
use Inviqa\StockIndicatorExport\Infrastructure\Repository\InMemoryProductRepository;
use Inviqa\StockIndicatorExport\Infrastructure\Repository\InMemoryStockIndicatorExportDocumentRepository;
use PHPUnit\Framework\Assert;

class StockIndicatorExportContext implements Context
{
    /** @var InMemoryProductRepository */
    private $productRepository;

    /** @var InMemoryStockIndicatorExportDocumentRepository */
    private $stockIndicatorExportDocumentRepository;

    /** @var ExportStockIndicatorCommandHandler */
    private $exportStockIndicatorCommandHandler;

    /** @var ExportStockIndicatorListCommandHandler */
    private $exportStockIndicatorListCommandHandler;

    /** @var ExportAllStockIndicatorCommandHandler */
    private $exportAllStockIndicatorCommandHandler;

    /** @var Product[] */
    private $catalog = [];

    /** @var Product|null */
    private $product = null;

    /** @var StockIndicatorExportDocument|null */
    private $stockIndicatorExportDocument = null;

    /** @var int */
    private $expectedNumberOfEntries = 0;

    /** @var ProductNotFoundException|null */
    private $exportError = null;

    /** @var StockIndicator|null */
    private $inspectedStockIndicator = null;

    public function __construct(
        InMemoryProductRepository $productRepository,
        InMemoryStockIndicatorExportDocumentRepository $stockIndicatorExportDocumentRepository,
        ExportStockIndicatorCommandHandler $exportStockIndicatorCommandHandler,
        ExportStockIndicatorListCommandHandler $exportStockIndicatorListCommandHandler,
        ExportAllStockIndicatorCommandHandler $exportAllStockIndicatorCommandHandler
    ) {
        $this->productRepository = $productRepository;
        $this->stockIndicatorExportDocumentRepository = $stockIndicatorExportDocumentRepository;
        $this->exportStockIndicatorCommandHandler = $exportStockIndicatorCommandHandler;
        $this->exportStockIndicatorListCommandHandler = $exportStockIndicatorListCommandHandler;
        $this->exportAllStockIndicatorCommandHandler = $exportAllStockIndicatorCommandHandler;
    }

    /**
     * @Given the catalog contains a product with sku :sku
     */
    public function theCatalogContainsAProductWithSku(ProductSku $productSku)
    {
        $product = Product::fromSku($productSku);
        $this->catalog[$product->sku()->toString()] = $product;
        $this->product = $product;
        $this->productRepository->add($product);
    }

    /**
     * @Given the catalog contains a product that has a stock level of :productStock
     */
    public function theCatalogContainsAProductThatHasAStockLevelOf(ProductStock $productStock)
    {
        $product = Product::fromSkuAndStock(ProductSku::fromString('test'), $productStock);
        $this->catalog[$product->sku()->toString()] = $product;
        $this->product = $product;
        $this->productRepository->add($product);
    }

    /**
     * @Given the catalog contains these products:
     */
    public function theCatalogContainsTheseProducts(ProductSkuList $productSkuList)
    {
        foreach ($productSkuList as $productSku) {
            $this->theCatalogContainsAProductWithSku($productSku);
        }
    }

    /**
     * @Given the catalog contains only these products:
     */
    public function theCatalogContainsOnlyTheseProducts(ProductSkuList $productSkuList)
    {
        $this->catalog = [];
        $this->productRepository->clear();
        $this->theCatalogContainsTheseProducts($productSkuList);
    }

    /**
     * @Given the catalog does not contain a product with sku :arg1
     */
    public function theCatalogDoesNotContainAProductWithSku(ProductSku $productSku)
    {
        $this->productRepository->remove($productSku);
    }

    /**
     * @Given the stock indicator export document has been generated for this product
     */
    public function theStockIndicatorExportDocumentHasBeenGeneratedForThisProduct()
    {
        $this->theUserRunsTheStockIndicatorExportFor($this->product->sku());
    }

    /**
     * @When the user runs the stock indicator export for this product
     */
    public function theUserRunsTheStockIndicatorExportForThisProduct()
    {
        Assert::assertNotNull($this->product);

        $this->theUserRunsTheStockIndicatorExportFor($this->product->sku());
    }

    /**
     * @When the user runs the stock indicator export for :productSku
     */
    public function theUserRunsTheStockIndicatorExportFor(ProductSku $productSku)
    {
        try {
            $documentId = StockIndicatorExportDocumentId::fromString('test');
            $command = new ExportStockIndicatorCommand($documentId->toString(), $productSku->toString());
            $this->exportStockIndicatorCommandHandler->handle($command);
            $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->findById($documentId);
        } catch (ProductNotFoundException $e) {
            $this->exportError = $e;
        }
    }

    /**
     * @When the user runs the stock indicator export for these products:
     */
    public function theUserRunsTheStockIndicatorExportForTheseProducts(ProductSkuList $productSkuList)
    {
        try {
            $documentId = StockIndicatorExportDocumentId::fromString('test');
            $command = new ExportStockIndicatorListCommand($documentId->toString(), $productSkuList->toStringArray());
            $this->exportStockIndicatorListCommandHandler->handle($command);
            $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->findById($documentId);
        } catch (ProductNotFoundException $e) {
            $this->exportError = $e;
        }
    }

    /**
     * @When the user runs the stock indicator export for the complete catalog
     */
    public function theUserRunsTheStockIndicatorExportForTheCompleteCatalog()
    {
        $documentId = StockIndicatorExportDocumentId::fromString('test');
        $command = new ExportAllStockIndicatorCommand($documentId->toString());
        $this->exportAllStockIndicatorCommandHandler->handle($command);
        $this->stockIndicatorExportDocument = $this->stockIndicatorExportDocumentRepository->findById($documentId);
    }

    /**
     * @When the user checks the stock indicator for this product in the document
     */
    public function theUserChecksTheStockIndicatorForThisProductInTheDocument()
    {
        Assert::assertNotNull($this->product);
        Assert::assertNotNull($this->stockIndicatorExportDocument);

        $this->inspectedStockIndicator = null;

        foreach ($this->stockIndicatorExportDocument as $documentEntry) {
            if ($documentEntry->sku()->equals($this->product->sku())) {
                $this->inspectedStockIndicator = $documentEntry->stockIndicator();
                break;
            }
        }
    }

    /**
     * @Then a product not found error for :productSku is shown
     */
    public function aProductNotFoundErrorForIsShown(ProductSku $productSku)
    {
        Assert::assertEquals(ProductNotFoundException::fromSku($productSku), $this->exportError);
    }

    /**
     * @Then a stock indicator export document is not generated
     */
    public function aStockIndicatorExportDocumentIsNotGenerated()
    {
        Assert::assertNull($this->stockIndicatorExportDocument);
    }

    /**
     * @Then the user sees a :expectedStockIndicator stock indicator
     */
    public function theUserSeesAStockIndicator(StockIndicator $expectedStockIndicator)
    {
        Assert::assertEquals($expectedStockIndicator, $this->inspectedStockIndicator);
    }

    /**
     * @Then a stock indicator export document is generated
     */
    public function aStockIndicatorExportDocumentIsGenerated()
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);
    }

    /**
     * @Then the document contains an entry for :productSku
     */
    public function theDocumentContainsAnEntryFor(ProductSku $productSku)
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);
        Assert::assertArrayHasKey($productSku->toString(), $this->catalog);
        Assert::assertTrue($this->stockIndicatorExportDocument->hasEntryWithSku($productSku));
        $this->expectedNumberOfEntries++;
    }

    /**
     * @Then the document does not have any further entries
     */
    public function theDocumentDoesNotHaveAnyFurtherEntries()
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);
        Assert::assertCount($this->expectedNumberOfEntries, $this->stockIndicatorExportDocument);
    }

    /**
     * @Then the document contains exactly one entry for each product in the catalog
     */
    public function theDocumentContainsExactlyOneEntryForEachProductInTheCatalog()
    {
        foreach ($this->catalog as $product) {
            $this->theDocumentContainsAnEntryFor($product->sku());
        }
    }
}
