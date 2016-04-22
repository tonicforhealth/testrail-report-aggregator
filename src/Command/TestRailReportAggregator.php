<?php

namespace  TonicForHealth\ReportAggregator\Command;

use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TonicForHealth\ReportAggregator\Report\JUnit\JUnitReport;
use TonicForHealth\ReportAggregator\Sync\TestRailSync;
use TonicForHealth\ReportAggregator\Transformer\TestRail\JUnitToTestRailRunTransformer;

/**
 * Class TestRailReportAggregator
 */
class TestRailReportAggregator extends Command
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var TestRailSync
     */
    private $testrailSync;

    /**
     * HealthCheck constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->setContainer($container);

        parent::__construct();
    }

    /**
     * @return TestRailSync
     */
    public function getTestrailSync()
    {
        return $this->testrailSync;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    protected function initDependency()
    {
        $this->setTestrailSync($this->getContainer()['testrail.sync']);
    }

    protected function configure()
    {
        $this
            ->setName('testrail-report-aggregator:sync')
            ->setDescription('Sync test case from result file (JUnit) to testrail')
            ->addArgument(
                'report_file',
                InputArgument::REQUIRED,
                'Report file path'
            )
            ->addArgument(
                'run_id',
                InputArgument::REQUIRED,
                'TestRail run id'
            )->addOption(
                'report_type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Report type',
                'junit'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initDependency();
        $reportFilePath = $input->getArgument('report_file');
        $reportType = $input->getOption('report_type');
        $runId = $input->getArgument('run_id');
        $testRailReport = null;
        if ($reportType === 'junit') {
            $report = new JunitReport($reportFilePath);
            $testRailReportA = new JUnitToTestRailRunTransformer($runId);
            $testRailReport = $testRailReportA->transform($report);
        }
        if (null !== $testRailReport) {
            $this->getTestrailSync()->sync($testRailReport);
            $this->getTestrailSync()->pushResults($testRailReport);
        }
    }

    /**
     * @param Container $container
     */
    protected function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @param TestRailSync $testrailSync
     */
    protected function setTestrailSync(TestRailSync $testrailSync)
    {
        $this->testrailSync = $testrailSync;
    }
}
