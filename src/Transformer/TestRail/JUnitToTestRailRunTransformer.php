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
            $attr = $testSuite->attributes;
            if (!$attr->getNamedItem('name')) {
                continue;
            }

            $name = $attr->getNamedItem('name')->nodeValue;
            $failures = $this->getFailuresCount($testSuite);

            $cases = new TestCase($name);

            if ($failures > 0) {
                $this->addFailures($testSuite, $cases);
            } else {
                $cases->getResults()->add(new Result());
            }

            $testRailReport->getCasesCollection()->add($cases);
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
    protected function getFailuresCount($testSuite)
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
     * @param $testSuite
     * @param $cases
     */
    protected function addFailures($testSuite, $cases)
    {
        /** @var DOMNode $childNode */
        foreach ($testSuite->childNodes as $childNode) {
            if ($childNode->nodeName === 'failure') {
                $result = new Result(Result::STATUS_FAILED);
                if (strlen($childNode->nodeValue) > 0) {
                    $result->setComment($childNode->nodeValue);
                } elseif (null !== $childNode->attributes->getNamedItem('message')) {
                    $result->setComment($childNode->attributes->getNamedItem('message')->nodeValue);
                }
                $cases->getResults()->add($result);
            }
        }
    }
}
