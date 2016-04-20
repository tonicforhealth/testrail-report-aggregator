# Package name
[![License](https://img.shields.io/github/license/tonicforhealth/testrail-report-aggregator.svg?maxAge=2592000)](LICENSE.md)
[![Build Status](https://travis-ci.org/tonicforhealth/testrail-report-aggregator.svg?branch=master)](https://travis-ci.org/tonicforhealth/testrail-report-aggregator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tonicforhealth/testrail-report-aggregator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tonicforhealth/testrail-report-aggregator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tonicforhealth/testrail-report-aggregator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tonicforhealth/testrail-report-aggregator/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e067099c-fbc2-4c9f-849c-d3c1960aa870/mini.png)](https://insight.sensiolabs.com/projects/e067099c-fbc2-4c9f-849c-d3c1960aa870)


A few words about package, why it could be usefull for someone 

## Installation using [Composer](http://getcomposer.org/)

```bash
$ composer require tonicforhealth/testrail-report-aggregator
```

## Usage

```php
<?php

        $testRunId = 1885;
        $apiUrl = 'https://test.testrail.com/index.php?/api/v2/';
        $user = dev@test.com;
        $passwordOrToken = 'chengeMePls'
        $junitXml = 'fixture/simple_junit_report.xml';

        //$pluginClient = new PluginClient($this->getMockClient());
        $authentication = new BasicAuth(
            $user,
            $passwordOrToken
        );
        $plugins[] = new AuthenticationPlugin($authentication);

        $pluginClient = new PluginClient(HttpClientDiscovery::find(), $plugins);

        $httpMethodsClient = new HttpMethodsClient($pluginClient, MessageFactoryDiscovery::find());

        $junitReport = new JunitReport($junitXml);

        $testRailReportA = new JUnitToTestRailRunTransformer($testRunId);

        $testRailSync = new TestRailSync($apiUrl, $httpMethodsClient);

        $testRailReport = $testRailReportA->transform($junitReport);

        $testRailSync->sync($testRailReport);

        $testRailSync->pushResults($testRailReport);

```