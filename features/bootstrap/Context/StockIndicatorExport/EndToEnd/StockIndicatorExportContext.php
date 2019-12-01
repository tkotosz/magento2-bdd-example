<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\EndToEnd;

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
use Magento\Catalog\Model\ProductFactory as MagentoProductFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\Assert;

class StockIndicatorExportContext implements Context
{
    /** @var Product|null */
    private $product = null;

    /** @var StockIndicatorExportDocument|null */
    private $stockIndicatorExportDocument = null;

    /** @var int */
    private $expectedNumberOfDocumentEntries = 0;

    /** @var bool */
    private $exportError = false;

    /** @var StockIndicator|null */
    private $inspectedStockIndicator = null;

    /** @var StockIndicatorExporter */
    private $stockIndicatorExporter;

    /** @var MagentoProductRepository */
    private $magentoProductRepository;

    /** @var MagentoProductFactory */
    private $magentoProductFactory;

    /** @var ResourceConnection */
    private $resourceConnection;

    public function __construct(
        StockIndicatorExporter $stockIndicatorExporter,
        MagentoProductRepository $magentoProductRepository,
        MagentoProductFactory $magentoProductFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->stockIndicatorExporter = $stockIndicatorExporter;
        $this->magentoProductRepository = $magentoProductRepository;
        $this->magentoProductFactory = $magentoProductFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        $this->resourceConnection->getConnection()->delete('catalog_product_entity');
        $this->resourceConnection->getConnection()->delete('url_rewrite');
        $this->resourceConnection->getConnection()->delete('inventory_source_item');
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
            // it seems magento doesn't delete the stock when the product is removed (missing foreign key)
            $this->resourceConnection->getConnection()->delete('inventory_source_item', ['sku=?' => $sku->toString()]);
        } catch (NoSuchEntityException $e) {
            // no-op already deleted
        }
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
        $exitCode = null;
        $command = sprintf(
            'bin/magento -q inviqa:stock-indicator:export %s',
            $this->product->sku()->toString()
        );

        system($command, $exitCode);

        if ($exitCode === 0) {
            $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
        } else {
            $this->exportError = true;
        }
    }

    /**
     * @When the user runs the stock indicator export for :sku
     */
    public function theUserRunsTheStockIndicatorExportForASku(Sku $sku)
    {
        $exitCode = null;
        $command = sprintf(
            'bin/magento -q inviqa:stock-indicator:export %s',
            $sku->toString()
        );

        system($command, $exitCode);

        if ($exitCode === 0) {
            $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
        } else {
            $this->exportError = true;
        }
    }

    /**
     * @When the user runs the stock indicator export for :firstSku and :secondSku
     */
    public function theUserRunsTheStockIndicatorExportForAListOfSkus(Sku $firstSku, Sku $secondSku)
    {
        $exitCode = null;
        $command = sprintf(
            'bin/magento -q inviqa:stock-indicator:export %s',
            implode(' ', [$firstSku->toString(), $secondSku->toString()])
        );

        system($command, $exitCode);

        if ($exitCode === 0) {
            $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
        } else {
            $this->exportError = true;
        }
    }

    /**
     * @When the user runs the stock indicator export for the complete catalog
     */
    public function theUserRunsTheStockIndicatorExportForTheCompleteCatalog()
    {
        $exitCode = null;
        $command = 'bin/magento -q inviqa:stock-indicator:export --full';

        system($command, $exitCode);

        if ($exitCode === 0) {
            $this->stockIndicatorExportDocument = $this->getLastExportedDocument();
        } else {
            $this->exportError = true;
        }
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
        // For now lets just check if there was an error during export
        Assert::assertEquals(true, $this->exportError);
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
