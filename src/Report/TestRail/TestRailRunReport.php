<?php

namespace TonicForHealth\ReportAggregator\Report\TestRail;

use TonicForHealth\ReportAggregator\Entity\TestCaseCollection;
use TonicForHealth\ReportAggregator\Report\ReportInterface;

/**
 * Class TestRailReport
 */
class TestRailRunReport implements ReportInterface
{
    /**
     * @var TestCaseCollection;
     */
    private $casesCollection;

    /**
     * @var int
     */
    private $testRunId;

    /**
     * TestRailReport constructor.
     *
     * @param int                     $testRunId
     * @param TestCaseCollection|null $casesCollection
     */
    public function __construct($testRunId, TestCaseCollection $casesCollection = null)
    {
        $this->setTestRunId($testRunId);

        if (null === $casesCollection) {
            $casesCollection = new TestCaseCollection();
        }

        $this->setCasesCollection($casesCollection);
    }

    /**
     * @return TestCaseCollection
     */
    public function getCasesCollection()
    {
        return $this->casesCollection;
    }

    /**
     * @return int
     */
    public function getTestRunId()
    {
        return $this->testRunId;
    }

    /**
     * @param TestCaseCollection $casesCollection
     */
    protected function setCasesCollection(TestCaseCollection $casesCollection)
    {
        $this->casesCollection = $casesCollection;
    }

    /**
     * @param int $testRunId
     */
    protected function setTestRunId($testRunId)
    {
        $this->testRunId = $testRunId;
    }
}
