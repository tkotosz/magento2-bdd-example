<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Integration;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Service\StockIndicatorExporter;
use Magento\Catalog\Api\ProductRepositoryInterface as MagentoProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\Assert;
use Magento\Catalog\Model\ProductFactory as MagentoProductFactory;
use Magento\Catalog\Model\Product as MagentoProduct;

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

    /** @var StockIndicatorExporter */
    private $stockIndicatorExporter;

    /** @var MagentoProductRepository */
    private $magentoProductRepository;

    /** @var MagentoProductFactory */
    private $magentoProductFactory;

    public function __construct(
        StockIndicatorExporter $stockIndicatorExporter,
        MagentoProductRepository $magentoProductRepository,
        MagentoProductFactory $magentoProductFactory
    ) {
        $this->stockIndicatorExporter = $stockIndicatorExporter;
        $this->magentoProductRepository = $magentoProductRepository;
        $this->magentoProductFactory = $magentoProductFactory;
    }

    /**
     * @Given there is a product in the catalog with sku :sku
     */
    public function thereIsAProductInTheCatalogWithSku(Sku $sku)
    {
        $product = Product::fromSku($sku);
        $magentoProduct = $this->magentoProductFactory->create();
        $magentoProduct->setSku($product->sku()->toString());
        $magentoProduct->setName('Test product ' . $product->sku()->toString());
        $magentoProduct->setTypeId('simple');
        $magentoProduct->setAttributeSetId(4);
        $magentoProduct->setPrice(10);
        $this->magentoProductRepository->save($magentoProduct);
        $this->product = $product;
    }

    /**
     * @Given there is a product in the catalog that has a stock level of :stock
     */
    public function thereIsAProductInTheCatalogThatHasAStockLevelOf(Stock $stock)
    {
        $product = Product::fromSkuAndStock(Sku::fromString(uniqid()), $stock);
        $magentoProduct = $this->magentoProductFactory->create();
        $magentoProduct->setSku($product->sku()->toString());
        $magentoProduct->setName('Test product ' . $product->sku()->toString());
        $magentoProduct->setTypeId('simple');
        $magentoProduct->setAttributeSetId(4);
        $magentoProduct->setPrice(10);
        $this->magentoProductRepository->save($magentoProduct);
        $magentoProduct = $this->magentoProductRepository->get($magentoProduct->getSku());
        $stockItem = $magentoProduct->getExtensionAttributes()->getStockItem();
        $stockItem->setQty($product->stock()->toInt());
        $this->magentoProductRepository->save($magentoProduct);
        $this->product = $product;
    }

    /**
     * @Given the product with sku :sku does not exists in the catalog
     */
    public function theProductWithSkuDoesNotExistsInTheCatalog(Sku $sku)
    {
        try {
            $this->magentoProductRepository->deleteById($sku->toString());
        } catch (NoSuchEntityException $e) {
            // no-op already deleted
        }
    }

    /**
     * @Given there are no other products in the catalog
     */
    public function thereAreNoOtherProductsInTheCatalog()
    {
        // TODO
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
        $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
    }

    /**
     * @When the user runs the stock indicator export for :sku
     */
    public function theUserRunsTheStockIndicatorExportForASku(Sku $sku)
    {
        try {
            $this->stockIndicatorExporter->export($sku);
            $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
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
            $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
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
        $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
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

    private function getLastExportedDocument(): ?StockIndicatorExportDocument
    {
        if (!is_file('/tmp/file.csv')) {
            return null;
        }

        $fp = fopen("/tmp/file.csv", "r");

        $stockIndicatorExportEntries = [];
        while (($data = fgetcsv($fp)) !== FALSE) {
            [$sku, $indicator] = $data;
            $stockIndicatorExportEntries[] = DocumentEntry::fromSkuAndStockIndicator(
                Sku::fromString($sku),
                StockIndicator::fromString($indicator)
            );
        }

        fclose($fp);

        return StockIndicatorExportDocument::fromEntries($stockIndicatorExportEntries);
    }
}
