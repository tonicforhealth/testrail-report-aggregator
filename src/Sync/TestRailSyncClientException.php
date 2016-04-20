<?php

namespace TonicForHealth\ReportAggregator\Sync;

use Exception;

/**
 * Class TestRailSyncException
 */
class TestRailSyncClientException extends TestRailSyncException
{
    /**
     * @param Exception $exception
     *
     * @return self
     */
    public static function clientError(Exception $exception)
    {
        return new self(
            sprintf('TestRail client error:%s', $exception->getMessage()),
            $exception->getCode()
        );
    }
}
