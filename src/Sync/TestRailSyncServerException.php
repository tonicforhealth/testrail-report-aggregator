<?php

namespace TonicForHealth\ReportAggregator\Sync;

/**
 * Class TestRailSyncServerException
 */
class TestRailSyncServerException extends TestRailSyncException
{
    /**
     * @param string $apiUrl
     * @param int    $httpCode
     * @param string $strError
     *
     * @return TestRailSyncServerException
     */
    public static function apiServerError($apiUrl, $httpCode, $strError)
    {
        return new self(
            sprintf(
                'TestRail server API path:%s returned not OK http code:%d error:%s',
                $apiUrl,
                $httpCode,
                $strError
            )
        );
    }
}
