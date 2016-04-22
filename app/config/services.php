<?php

/** @var \Pimple\Container $container */
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Plugin\AuthenticationPlugin;
use Http\Client\Plugin\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\BasicAuth;
use Pimple\Container;
use TonicForHealth\ReportAggregator\Sync\TestRailSync;

$config = $container['config'];

$container['testrail.api_url'] = $config['testrail']['api_url'];
$container['testrail.user'] = $config['testrail']['user'];
$container['testrail.password'] = $config['testrail']['password'];
$container['testrail.base_auth'] = function (Container $c) {
    return new BasicAuth(
        $c['testrail.user'],
        $c['testrail.password']
    );
};

$container['testrail.auth'] = function (Container $c) {
    return new AuthenticationPlugin(
        $c['testrail.base_auth']
    );
};

$container['testrail.plugin_client'] = function (Container $c) {
    return new PluginClient(
        HttpClientDiscovery::find(),
        [$c['testrail.auth']]
    );
};

$container['testrail.client'] = function (Container $c) {
    return new HttpMethodsClient(
        $c['testrail.plugin_client'],
        MessageFactoryDiscovery::find()
    );
};

$container['testrail.sync'] = function (Container $c) {
    return new TestRailSync(
        $c['testrail.api_url'],
        $c['testrail.client']
    );
};
