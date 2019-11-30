<?php

namespace spec\Inviqa\StockIndicatorExport\Domain\Service;

use Inviqa\StockIndicatorExport\Domain\Model\Product;
use Inviqa\StockIndicatorExport\Domain\Model\Product\Sku;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Model\ProductList;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicator;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument;
use Inviqa\StockIndicatorExport\Domain\Model\StockIndicatorExportDocument\DocumentEntry;
use Inviqa\StockIndicatorExport\Domain\Repository\Catalog;
use Inviqa\StockIndicatorExport\Domain\Repository\StockIndicatorExportDocumentRepository;
use Inviqa\StockIndicatorExport\Domain\Service\StockIndicatorExporter;
use PhpSpec\ObjectBehavior;

class StockIndicatorExporterSpec extends ObjectBehavior
{
    function let(
        Catalog $catalog,
        StockIndicatorExportDocumentRepository $exportDocumentRepository
    ) {
        $this->beConstructedWith($catalog, $exportDocumentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StockIndicatorExporter::class);
    }

    function it_should_export_a_product_by_sku(
        Catalog $catalog,
        StockIndicatorExportDocumentRepository $exportDocumentRepository
    ) {
        $product = Product::fromSku(Sku::fromString('foo'));
        $documentEntry = DocumentEntry::fromSkuAndStockIndicator($product->sku(), StockIndicator::red());

        $catalog->findBySku($product->sku())->willReturn($product);

        $this->export($product->sku());

        $expectedDocument = StockIndicatorExportDocument::fromEntries([$documentEntry]);
        $exportDocumentRepository->save($expectedDocument)->shouldHaveBeenCalled();
    }

    function it_should_export_a_list_product_by_skus(
        Catalog $catalog,
        StockIndicatorExportDocumentRepository $exportDocumentRepository
    ) {
        $productFoo = Product::fromSku(Sku::fromString('foo'));
        $productBar = Product::fromSku(Sku::fromString('bar'));
        $skuList = SkuList::fromSkus([$productFoo->sku(), $productBar->sku()]);
        $documentEntryFoo = DocumentEntry::fromSkuAndStockIndicator($productFoo->sku(), StockIndicator::red());
        $documentEntryBar = DocumentEntry::fromSkuAndStockIndicator($productBar->sku(), StockIndicator::red());

        $catalog->findBySkuList($skuList)->willReturn(ProductList::fromProducts([$productFoo, $productBar]));

        $this->exportList($skuList);

        $expectedDocument = StockIndicatorExportDocument::fromEntries([$documentEntryFoo, $documentEntryBar]);
        $exportDocumentRepository->save($expectedDocument)->shouldHaveBeenCalled();
    }

    function it_should_export_a_the_whole_catalog(
        Catalog $catalog,
        StockIndicatorExportDocumentRepository $exportDocumentRepository
    ) {
        $productFoo = Product::fromSku(Sku::fromString('foo'));
        $productBar = Product::fromSku(Sku::fromString('bar'));
        $documentEntryFoo = DocumentEntry::fromSkuAndStockIndicator($productFoo->sku(), StockIndicator::red());
        $documentEntryBar = DocumentEntry::fromSkuAndStockIndicator($productBar->sku(), StockIndicator::red());

        $catalog->findAll()->willReturn(ProductList::fromProducts([$productFoo, $productBar]));

        $this->exportAll();

        $expectedDocument = StockIndicatorExportDocument::fromEntries([$documentEntryFoo, $documentEntryBar]);
        $exportDocumentRepository->save($expectedDocument)->shouldHaveBeenCalled();
    }
}
