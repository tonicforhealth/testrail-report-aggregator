<?php

namespace TonicForHealth\ReportAggregator\Report\JUnit;

/**
 * Class JUnitReportInvalidXml
 */
class JUnitReportInvalidXml extends JUnitReportException
{
    public static function invalidJUnitXml($reportPath)
    {
        return new self(sprintf('Invalid xml JUnit file %s', $reportPath));
    }
}
