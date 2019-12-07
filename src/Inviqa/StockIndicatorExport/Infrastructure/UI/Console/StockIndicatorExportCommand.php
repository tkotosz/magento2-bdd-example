<?php

declare(strict_types=1);

namespace Inviqa\StockIndicatorExport\Infrastructure\UI\Console;

use Exception;
use InvalidArgumentException;
use Inviqa\StockIndicatorExport\Application\ExportAllStockIndicator\ExportAllStockIndicatorCommand;
use Inviqa\StockIndicatorExport\Application\ExportAllStockIndicator\ExportAllStockIndicatorCommandHandler;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicator\ExportStockIndicatorCommand;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicator\ExportStockIndicatorCommandHandler;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicatorList\ExportStockIndicatorListCommand;
use Inviqa\StockIndicatorExport\Application\ExportStockIndicatorList\ExportStockIndicatorListCommandHandler;
use Inviqa\StockIndicatorExport\Domain\Exception\ProductNotFoundException;
use Inviqa\StockIndicatorExport\Domain\Exception\StockIndicatorExportDocumentSaveFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StockIndicatorExportCommand extends Command
{
    /** @var ExportStockIndicatorCommandHandler */
    private $exportStockIndicatorCommandHandler;

    /** @var ExportStockIndicatorListCommandHandler */
    private $exportStockIndicatorListCommandHandler;

    /** @var ExportAllStockIndicatorCommandHandler */
    private $exportAllStockIndicatorCommandHandler;

    public function __construct(
        ExportStockIndicatorCommandHandler $exportStockIndicatorCommandHandler,
        ExportStockIndicatorListCommandHandler $exportStockIndicatorListCommandHandler,
        ExportAllStockIndicatorCommandHandler $exportAllStockIndicatorCommandHandler
    ) {
        parent::__construct();

        $this->exportStockIndicatorCommandHandler = $exportStockIndicatorCommandHandler;
        $this->exportStockIndicatorListCommandHandler = $exportStockIndicatorListCommandHandler;
        $this->exportAllStockIndicatorCommandHandler = $exportAllStockIndicatorCommandHandler;
    }

    protected function configure(): void
    {
        $this
            ->setName('inviqa:stock-indicator:export')
            ->setDescription('Exports stock indicators for given product(s) or for the complete catalog')
            ->addArgument('document_id', InputArgument::REQUIRED, 'Document ID')
            ->addArgument('skus', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Sku List', [])
            ->addOption('full', '-f', InputOption::VALUE_NONE, 'Full catalog export')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->runExport($input);
            $output->writeln('Done.');
            return 0;
        } catch (Exception $e) {
            $output->writeln('Export failed with error: ' . $e->getMessage());
            $output->writeln($e->getTraceAsString(), OutputInterface::VERBOSITY_DEBUG);
            return 1;
        }
    }

    /**
     * @param InputInterface $input
     *
     * @throws ProductNotFoundException
     * @throws StockIndicatorExportDocumentSaveFailedException
     */
    private function runExport(InputInterface $input): void
    {
        $documentId = $input->getArgument('document_id');
        $skuList = (array) $input->getArgument('skus');

        if (!is_string($documentId)) {
            throw new InvalidArgumentException('Document ID must be string');
        }

        if ((bool) $input->getOption('full')) {
            $this->exportAllStockIndicatorCommandHandler->handle(new ExportAllStockIndicatorCommand($documentId));
            return;
        }

        if (count($skuList) === 1) {
            $this->exportStockIndicatorCommandHandler->handle(
                new ExportStockIndicatorCommand($documentId, array_shift($skuList))
            );

            return;
        }

        $this->exportStockIndicatorListCommandHandler->handle(
            new ExportStockIndicatorListCommand($documentId, $skuList)
        );

        return;
    }
}
