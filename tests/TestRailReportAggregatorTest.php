<?php

namespace TonicForHealth\ReportAggregator\Test;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Plugin\AuthenticationPlugin;
use Http\Client\Plugin\PluginClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\BasicAuth;
use Http\Mock\Client as MockClient;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TonicForHealth\ReportAggregator\Report\JUnit\JUnitReport;
use TonicForHealth\ReportAggregator\Sync\TestRailSync;
use TonicForHealth\ReportAggregator\Transformer\TestRail\JUnitToTestRailRunTransformer;

class TestRailReportAggregatorTest extends PHPUnit_Framework_TestCase
{
    const BASE_TEST_RUN_ID = 4321;
    const TESTRAIL_API_URL = 'https://tonic.testrail.com/index.php?/api/v2/';
    const TESTRAIL_USER = 'test@test.com';
    const TESTRAIL_TOKEN = 'FSDFSDWFFSSsKz1wIdT-WDfIHEOH$FHEFH23IFGF2';
    /**
     * @var MockClient
     */
    private $mockClient;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|HttpMethodsClient;
     */
    private $httpMethodsClient;

    /**
     * Set up bese env for test
     */
    public function setUp()
    {
        $this->setMockClient(new MockClient());

        $authentication = new BasicAuth(
            self::TESTRAIL_USER,
            self::TESTRAIL_TOKEN
        );
        $plugins[] = new AuthenticationPlugin($authentication);

        $pluginClient = new PluginClient($this->getMockClient(), $plugins);

        $this->setHttpMethodsClient(
            $this->getMockBuilder(HttpMethodsClient::class)
                ->enableProxyingToOriginalMethods()
                ->setConstructorArgs([$pluginClient, MessageFactoryDiscovery::find()])
                ->getMock()
        );
    }

    /**
     * Process junit report
     */
    public function testProcessJunitReport()
    {
        $this->setUpBaseTestrunResponse();

        $testRunId = self::BASE_TEST_RUN_ID;

        $junitReport = new JUnitReport($this->getSimpleJUnitFixturePath());

        $testRailReportA = new JUnitToTestRailRunTransformer($testRunId);

        $testRailSync = new TestRailSync(
            self::TESTRAIL_API_URL,
            $this->getHttpMethodsClient()
        );

        $testRailReport = $testRailReportA->transform($junitReport);

        $testRailSync->sync($testRailReport);

        $this->expectsAddResultRequest();

        $testRailSync->pushResults($testRailReport);


    }

    /**
     * @return MockClient
     */
    protected function getMockClient()
    {
        return $this->mockClient;
    }

    /**
     * @param MockClient $mockClient
     */
    protected function setMockClient(MockClient $mockClient)
    {
        $this->mockClient = $mockClient;
    }

    /**
     * @param $responseBody
     * @param $responseCode
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ResponseInterface
     */
    protected function getMockResponse($responseBody, $responseCode)
    {
        $streamMock = $this->getMock(StreamInterface::class);

        $streamMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($responseBody);

        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->once())->method('getBody')->willReturn($streamMock);
        $response->expects($this->once())->method('getStatusCode')->willReturn($responseCode);

        return $response;
    }

    /**
     * @return HttpMethodsClient|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHttpMethodsClient()
    {
        return $this->httpMethodsClient;
    }

    /**
     * @param HttpMethodsClient|PHPUnit_Framework_MockObject_MockObject $httpMethodsClient
     */
    protected function setHttpMethodsClient(HttpMethodsClient $httpMethodsClient)
    {
        $this->httpMethodsClient = $httpMethodsClient;
    }

    /**
     * @return string
     */
    protected function getSimpleJUnitFixturePath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'fixture/simple_junit_report.xml';
    }

    /**
     * @return string
     */
    protected function getBaseTestrunResponseFixturePath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'fixture/base_testrun_response.json';
    }
    /**
     * @return string
     */
    protected function getAddResultsRequestFixture()
    {
        return trim(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'fixture/add_results_request.json'));
    }

    protected function setUpBaseTestrunResponse()
    {
        $responseBody = file_get_contents($this->getBaseTestrunResponseFixturePath());

        $responseCode = 200;

        $responseMock = $this->getMockResponse($responseBody, $responseCode);

        $this->getMockClient()->addResponse($responseMock);
    }

    protected function expectsAddResultRequest()
    {
        $this->getHttpMethodsClient()
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->identicalTo(['Content-type' => 'application/json']),
                $this->identicalTo($this->getAddResultsRequestFixture())
            );
    }
}
