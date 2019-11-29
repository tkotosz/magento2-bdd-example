<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Domain;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use PHPUnit\Framework\Assert;
use RuntimeException;

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

    /** @var RuntimeException|null */
    private $exportError = null;

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
     * @Transform /^([red|yellow|green]+) stock indicator$/
     */
    public function transformStockIndicator(string $type): StockIndicator
    {
        return StockIndicator::fromString(strtoupper($type));
    }

    /**
     * @Given there is a product with sku :sku in the catalog that has a stock level of :stock
     */
    public function thereIsAProductWithSkuInTheCatalogThatHasAStockLevelOf(Sku $sku, Stock $stock)
    {
        $product = Product::fromSkuAndStock($sku, $stock);
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
     * @Given the product with sku :sku does not exists in the catalog
     */
    public function theProductWithSkuDoesNotExistsInTheCatalog(Sku $sku)
    {
        foreach ($this->catalog as $key => $product) {
            if ($product->sku()->equals($sku)) {
                unset($this->catalog[$key]);
            }
        }
    }

    /**
     * @When I run the stock indicator export for that product
     */
    public function iRunTheStockIndicatorExportForThatProduct()
    {
        if ($this->product === null) {
            $this->exportError = new RuntimeException('Product does not exists');
        } else {
            $stockIndicator = StockIndicator::fromProductStock($this->product->stock());
            $exportEntry = DocumentEntry::fromSkuAndStockIndicator($this->product->sku(), $stockIndicator);
            $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries([$exportEntry]);
        }

    }

    /**
     * @When I run the stock indicator export for the catalog
     */
    public function iRunTheStockIndicatorExportForTheCatalog()
    {
        $exportEntries = [];

        foreach ($this->catalog as $product) {
            $stockIndicator = StockIndicator::fromProductStock($product->stock());
            $exportEntries[] = DocumentEntry::fromSkuAndStockIndicator($product->sku(), $stockIndicator);
        }

        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries($exportEntries);
    }

    /**
     * @When I run the stock indicator export for :firstSku and :secondSku
     */
    public function iRunTheStockIndicatorExportForTheAListOfProducts(Sku $firstSku, Sku $secondSku)
    {
        $skuList = SkuList::fromSkus([$firstSku, $secondSku]);
        $products = [];
        foreach ($this->catalog as $product) {
            if ($skuList->has($product->sku())) {
                $products[] = $product;
            }
        }
        $productList = ProductList::fromProducts($products);

        $exportEntries = [];

        foreach ($productList as $product) {
            $stockIndicator = StockIndicator::fromProductStock($product->stock());
            $exportEntries[] = DocumentEntry::fromSkuAndStockIndicator($product->sku(), $stockIndicator);
        }

        $this->stockIndicatorExportDocument = StockIndicatorExportDocument::fromEntries($exportEntries);
    }

    /**
     * @Then a stock indicator export document should be generated
     */
    public function iShouldGetAStockIndicatorExportDocument()
    {
        Assert::assertInstanceOf(StockIndicatorExportDocument::class, $this->stockIndicatorExportDocument);
    }

    /**
     * @Then /^the document should contain an entry for "([^"]+)" with a (([red|yellow|green]+) stock indicator)$/
     */
    public function theDocumentShouldContainAnEntryForProductWithARedStockIndicator(Sku $sku, StockIndicator $expectedStockIndicator)
    {
        $productDocumentEntry = null;

        foreach ($this->stockIndicatorExportDocument as $documentEntry) {
            if ($documentEntry->sku()->equals($sku)) {
                $productDocumentEntry = $documentEntry;
            }
        }

        Assert::assertNotNull($productDocumentEntry);
        Assert::assertEquals($expectedStockIndicator, $productDocumentEntry->stockIndicator());
        $this->expectedNumberOfEntries++;
    }

    /**
     * @Then the document should not have any further entries
     */
    public function theDocumentShouldNotHaveAnyFurtherEntries()
    {
        Assert::assertNotNull($this->stockIndicatorExportDocument);
        Assert::assertCount($this->expectedNumberOfEntries, $this->stockIndicatorExportDocument);
    }

    /**
     * @Then I should get an error about that the product does not exists
     */
    public function iShouldGetAnErrorAboutThatTheProductDoesNotExists()
    {
        Assert::assertInstanceOf(RuntimeException::class, $this->exportError);
        Assert::assertEquals($this->exportError->getMessage(), 'Product does not exists');
    }

    /**
     * @Then a stock indicator export document should not be generated
     */
    public function aStockIndicatorExportDocumentShouldNotBeGenerated()
    {
        Assert::assertNull($this->stockIndicatorExportDocument);
    }
}
