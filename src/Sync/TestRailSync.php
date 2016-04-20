<?php

namespace TonicForHealth\ReportAggregator\Sync;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception as HttpClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TonicForHealth\ReportAggregator\Entity\Result;
use TonicForHealth\ReportAggregator\Entity\TestCase;
use TonicForHealth\ReportAggregator\Report\TestRail\TestRailRunReport;

/**
 * Class TestRailSync
 */
class TestRailSync
{
    /**
     * @var string;
     */
    private $apiUrl;

    /**
     * @var HttpMethodsClient
     */
    private $httpMethodsClient;

    /**
     * TestRailSync constructor.
     */
    public function __construct($apiUrl, HttpMethodsClient $httpMethodsClient)
    {
        $this->setApiUrl($apiUrl);

        $this->setHttpMethodsClient($httpMethodsClient);
    }

    /**
     * @param TestRailRunReport $testRailRunReport
     */
    public function sync(TestRailRunReport $testRailRunReport)
    {
        $testCases = $this->getTest($testRailRunReport->getTestRunId());

        if (count($testCases) > 0) {
            foreach ($testCases as $testCase) {
                $normalizeTitle = static::normalizeStrToCaseIdent($testCase->title);
                $caseId = $testCase->case_id;
                $testId = $testCase->id;
                /** @var TestCase $case */
                $case = $testRailRunReport->getCasesCollection()->find(
                    $this->findByNormalizeTitleCallback($normalizeTitle)
                );
                if ($case) {
                    $case->setId($caseId);
                    $case->setTestId($testId);
                }
            }
        }
    }

    /**
     * @param TestRailRunReport $testRailReport
     *
     * @throws TestRailSyncException
     * @throws TestRailSyncClientException
     */
    public function pushResults(TestRailRunReport $testRailReport)
    {
        $this->addResults($testRailReport, $testRailReport->getTestRunId());
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @return HttpMethodsClient
     */
    public function getHttpMethodsClient()
    {
        return $this->httpMethodsClient;
    }

    /**
     * @param string $apiUrl
     */
    protected function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param HttpMethodsClient $httpMethodsClient
     */
    protected function setHttpMethodsClient(HttpMethodsClient $httpMethodsClient)
    {
        $this->httpMethodsClient = $httpMethodsClient;
    }

    /**
     * @param $normalizeTitle
     *
     * @return \Closure
     */
    protected function findByNormalizeTitleCallback($normalizeTitle)
    {
        return function (TestCase $case) use ($normalizeTitle) {
            return static::normalizeStrToCaseIdent($case->getTitle()) === $normalizeTitle;
        };
    }

    protected function getResourceUrl($resourcePath)
    {
        return $this->getApiUrl().$resourcePath;
    }

    /**
     * Normalize string to case ident
     *
     * @param $caseName
     *
     * @return string
     */
    protected static function normalizeStrToCaseIdent($caseName)
    {
        return preg_replace('/(^[\w._-]+\.[\w]+\.[\w]+)?.*/is', '\\1', $caseName);
    }

    /**
     * @param TestRailRunReport $testRailReport
     *
     * @return array
     */
    protected function genResultsList(TestRailRunReport $testRailReport)
    {
        $results = [];

        /** @var TestCase $testCase */
        foreach ($testRailReport->getCasesCollection() as $testCase) {
            if (null === $testCase->getId()) {
                continue;
            }
            /** @var Result $result */
            foreach ($testCase->getResults() as $result) {
                $results[] = $this->getResult($testCase, $result);
            }
        }

        return $results;
    }

    /**
     * @param TestCase $testCase
     * @param Result   $result
     *
     * @return object
     */
    protected function getResult(TestCase $testCase, Result $result)
    {
        $result = (object) [
            'test_id' => $testCase->getTestId(),
            'status_id' => $result->getStatusId(),
            'comment' => $result->getComment(),
        ];

        return $result;
    }

    /**
     * @param $testRunId
     *
     * @return array
     *
     * @throws TestRailSyncClientException
     * @throws TestRailSyncServerException
     */
    protected function getTest($testRunId)
    {
        try {
            $apiUrlResource = $this->getResourceUrl(sprintf('/get_tests/%d', $testRunId));
            $response = $this->getHttpMethodsClient()->get(
                $apiUrlResource,
                ['Content-type' => 'application/json']
            );
        } catch (HttpClientException $e) {
            throw TestRailSyncClientException::clientError($e);
        }

        $this->validationRequest($response, $apiUrlResource);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param TestRailRunReport $testRailReport
     * @param $testRunId
     *
     * @return ResponseInterface
     *
     * @throws TestRailSyncServerException
     * @throws TestRailSyncClientException
     */
    protected function addResults(TestRailRunReport $testRailReport, $testRunId)
    {
        try {
            $apiUrlResource = $this->getResourceUrl(sprintf('/add_results/%d', $testRunId));
            $response = $this->getHttpMethodsClient()->post(
                $apiUrlResource,
                ['Content-type' => 'application/json'],
                json_encode([
                    'results' => $this->genResultsList($testRailReport),
                ])
            );
        } catch (HttpClientException $e) {
            throw TestRailSyncClientException::clientError($e);
        }

        $this->validationRequest($response, $apiUrlResource);

        return $response;
    }

    /**
     * @param ResponseInterface $request
     * @param $apiUrlResource
     *
     * @throws TestRailSyncServerException
     */
    protected function validationRequest(ResponseInterface $request, $apiUrlResource)
    {
        if ($request->getStatusCode() !== 200) {
            $httpCode = $request->getStatusCode();
            $requestObj = json_decode($request->getBody()->getContents());

            $errorStr = '';
            if ($requestObj && isset($requestObj->error)) {
                $errorStr = $requestObj->error;
            }

            throw TestRailSyncServerException::apiServerError($apiUrlResource, $httpCode, $errorStr);
        }
    }
}
