<?php

namespace Inviqa\StockIndicatorExport\Infrastructure\UI\Console;

use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Model\Product\SkuList;
use Inviqa\StockIndicatorExport\Domain\Service\StockIndicatorExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StockIndicatorExportCommand extends Command
{
    /** @var StockIndicatorExporter */
    private $stockIndicatorExporter;

    public function __construct(StockIndicatorExporter $stockIndicatorExporter)
    {
        $this->stockIndicatorExporter = $stockIndicatorExporter;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('inviqa:stock-indicator:export')
            ->setDescription('Exports stock indicators for given product(s) or for the complete catalog')
            ->addArgument('skus', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Sku List', [])
            ->addOption('full', '-f', InputOption::VALUE_NONE, 'Full catalog export')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $skuList = (array) $input->getArgument('skus');

        if ((bool) $input->getOption('full')) {
            $this->stockIndicatorExporter->exportAll();

            return 0;
        }

        if ($skuList === []) {
            $output->writeln('Please specify a list of skus or the --full option');
        }

        try {
            $this->stockIndicatorExporter->exportList(SkuList::fromStrings($skuList));
            $output->writeln('Done.');

            return 0;
        } catch (ProductNotFoundException $e) {
            $output->writeln('Export failed with error: ' . $e->getMessage());
            $output->writeln($e->getTraceAsString(), OutputInterface::VERBOSITY_DEBUG);

            return 1;
        }
    }
}
