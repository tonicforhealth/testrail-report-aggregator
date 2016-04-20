<?php

namespace TonicForHealth\ReportAggregator\Transformer\TestRail;

use DOMNode;
use TonicForHealth\ReportAggregator\Entity\TestCase;
use TonicForHealth\ReportAggregator\Entity\Result;
use TonicForHealth\ReportAggregator\Report\JUnit\JUnitReport;
use TonicForHealth\ReportAggregator\Report\TestRail\TestRailRunReport;

/**
 * Class JUnitToTestRailRunTransformer
 */
class JUnitToTestRailRunTransformer
{
    /**
     * @var int;
     */
    private $testRunId;

    /**
     * JUnitToTestRailRunTransformer constructor.
     *
     * @param null $testRunId
     */
    public function __construct($testRunId)
    {
        $this->setTestRunId($testRunId);
    }

    /**
     * @param JUnitReport $report
     * @param int         $testRunId
     *
     * @return TestRailRunReport
     */
    public function transform(JUnitReport $report, $testRunId = null)
    {
        $testRunId = null === $testRunId ? $this->getTestRunId() : $testRunId;

        $testRailReport = new TestRailRunReport($testRunId);
        $testSuites = $report->getXpath()->query('/testsuites/testsuite');

        /** @var DOMNode $testSuite */
        foreach ($testSuites as $testSuite) {
            if (!$testSuite->attributes->getNamedItem('name')) {
                continue;
            }
            $this->transformTestCase($testSuite, $testRailReport);
        }

        return $testRailReport;
    }

    /**
     * @return int
     */
    public function getTestRunId()
    {
        return $this->testRunId;
    }

    /**
     * @param int $testRunId
     */
    protected function setTestRunId($testRunId)
    {
        $this->testRunId = $testRunId;
    }

    /**
     * @param DOMNode $testSuite
     *
     * @return int
     */
    protected function getFailuresCount(DOMNode $testSuite)
    {
        $failures = 0;
        if ($testSuite->attributes->getNamedItem('failures')) {
            $failures = $testSuite->attributes->getNamedItem('failures')->nodeValue;

            return $failures;
        } else {
            foreach ($testSuite->childNodes as $childItem) {
                if ($childItem->nodeName === 'failure') {
                    $failures++;
                }
            }

            return $failures;
        }
    }

    /**
     * @param DOMNode  $testSuite
     * @param TestCase $case
     */
    protected function addFailures(DOMNode $testSuite, TestCase $case)
    {
        /** @var DOMNode $childNode */
        foreach ($testSuite->childNodes as $childNode) {
            if ($childNode->nodeName === 'failure') {
                $this->addFailure($case, $childNode);
            }
        }
    }

    /**
     * @param TestCase $case
     * @param DOMNode  $childNode
     */
    protected function addFailure(TestCase $case, DOMNode $childNode)
    {
        $result = new Result(Result::STATUS_FAILED);
        if (strlen($childNode->nodeValue) > 0) {
            $result->setComment($childNode->nodeValue);
        } elseif (null !== $childNode->attributes->getNamedItem('message')) {
            $result->setComment($childNode->attributes->getNamedItem('message')->nodeValue);
        }
        $case->getResults()->add($result);
    }

    /**
     * @param DOMNode           $testSuite
     * @param TestRailRunReport $testRailReport
     */
    protected function transformTestCase(DOMNode $testSuite, TestRailRunReport $testRailReport)
    {
        $attr = $testSuite->attributes;
        $name = $attr->getNamedItem('name')->nodeValue;
        $failures = $this->getFailuresCount($testSuite);

        $case = new TestCase($name);

        if ($failures > 0) {
            $this->addFailures($testSuite, $case);
        } else {
            $case->getResults()->add(new Result());
        }

        $testRailReport->getCasesCollection()->add($case);
    }
}
