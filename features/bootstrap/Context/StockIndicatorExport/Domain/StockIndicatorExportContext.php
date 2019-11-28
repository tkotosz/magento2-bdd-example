<?php

namespace Inviqa\Acceptance\Context\StockIndicatorExport\Domain;

use Behat\Behat\Context\Context;
use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Stock;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use PHPUnit\Framework\Assert;

class StockIndicatorExportContext implements Context
{
    /** @var Product[] */
    private $catalog = [];

    /** @var Product|null */
    private $product = null;

    /** @var StockIndicator */
    private $exportedStockIndicator;

    /** @var StockIndicator[] */
    private $exportedStockIndicators = [];

    /**
     * @Given /^there is a product in the catalog that has a stock level of (\d+)$/
     */
    public function thereIsAProductInTheCatalogThatHasAStockLevelOf(int $stock)
    {
        $product = Product::fromSkuAndStock(Sku::fromString('inviqa-t-shirt-size-l'), Stock::fromInt($stock));
        $this->catalog[] = $product;
        $this->product = $product;
    }

    /**
     * @Given /^there is a product with sku ([^ ]*) in the catalog that has a stock level of (\d+)$/
     */
    public function thereIsAProductWithSkuInTheCatalogThatHasAStockLevelOf(string $sku, int $stock)
    {
        $this->catalog[] = Product::fromSkuAndStock(Sku::fromString($sku), Stock::fromInt($stock));
    }

    /**
     * @Given there are no other products in the catalog
     */
    public function thereAreNoOtherProductsInTheCatalog()
    {
        // no-op
    }

    /**
     * @When I export the stock indicator for this product
     */
    public function iExportTheStockIndicatorForThisProduct()
    {
        $this->exportedStockIndicator = StockIndicator::for($this->product);
    }

    /**
     * @When I export the stock indicator for the whole catalog
     */
    public function iExportTheStockIndicatorForAllProducts()
    {
        $this->exportedStockIndicators = [];

        foreach ($this->catalog as $product) {
            $this->exportedStockIndicators[$product->sku()->toString()] = StockIndicator::for($product);
        }
    }

    /**
     * @Then this product should get a red stock indicator
     */
    public function thisProductShouldGetARedStockIndicator()
    {
        Assert::assertTrue($this->exportedStockIndicator->sameAs(StockIndicator::red()));
    }

    /**
     * @Then this product should get a yellow stock indicator
     */
    public function thisProductShouldGetAYellowStockIndicator()
    {
        Assert::assertTrue($this->exportedStockIndicator->sameAs(StockIndicator::yellow()));
    }

    /**
     * @Then this product should get a green stock indicator
     */
    public function thisProductShouldGetAGreenStockIndicator()
    {
        Assert::assertTrue($this->exportedStockIndicator->sameAs(StockIndicator::green()));
    }

    /**
     * @Then /^the ([^ ]*) product should get a red stock indicator$/
     */
    public function theProductShouldGetARedStockIndicator(string $sku)
    {
        Assert::assertTrue(isset($this->exportedStockIndicators[$sku]));
        Assert::assertTrue($this->exportedStockIndicators[$sku]->sameAs(StockIndicator::red()));
    }

    /**
     * @Then /^the ([^ ]*) product should get a yellow stock indicator$/
     */
    public function theProductShouldGetAYellowStockIndicator(string $sku)
    {
        Assert::assertTrue(isset($this->exportedStockIndicators[$sku]));
        Assert::assertTrue($this->exportedStockIndicators[$sku]->sameAs(StockIndicator::yellow()));
    }

    /**
     * @Then /^the ([^ ]*) product should get a green stock indicator$/
     */
    public function theProductShouldGetAGreenStockIndicator(string $sku)
    {
        Assert::assertTrue(isset($this->exportedStockIndicators[$sku]));
        Assert::assertTrue($this->exportedStockIndicators[$sku]->sameAs(StockIndicator::green()));
    }
}
