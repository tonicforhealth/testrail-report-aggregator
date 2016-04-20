<?php

namespace TonicForHealth\ReportAggregator\Report\JUnit;

/**
 * Class JUnitReportNotExist
 */
class JUnitReportNotExist extends JUnitReportException
{
    /**
     * @param $reportPath
     *
     * @return JUnitReportNotExist
     */
    public static function fileNotExist($reportPath)
    {
        return new self(sprintf('File %s didn\'t exist or readable.', $reportPath));
    }
}
