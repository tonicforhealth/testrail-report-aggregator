<?php

namespace TonicForHealth\ReportAggregator\Sync;

/**
 * Class TestRailSyncResultsEmptyException
 */
class TestRailSyncResultsEmptyException  extends TestRailSyncException
{
    /**
     * @param Exception $exception
     *
     * @return self
     */
    public static function resultsEmpty()
    {
        return new self('Nothing to push, result from TestRailReport is empty.');
    }
}
