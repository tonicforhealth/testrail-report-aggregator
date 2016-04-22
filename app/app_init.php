<?php

define('CONFIG_DEFAULT_PATH', __DIR__.'/../app/config/');
define('CONFIG_DEFAULT_FILE', 'config.php');
define('PARAMETER_DEFAULT_FILE', 'parameter.php');
define('AUTOLOAD_PATH', __DIR__.'/../vendor/autoload.php');

// include the composer autoloader
require_once AUTOLOAD_PATH;

$config = [];

$configFile = CONFIG_DEFAULT_PATH.CONFIG_DEFAULT_FILE;

$config = require $configFile;
$config = array_replace_recursive($config, $config);

$parameterFile = CONFIG_DEFAULT_PATH.PARAMETER_DEFAULT_FILE;
if (is_readable($parameterFile)) {
    $parameter = require $parameterFile;
    $config = array_replace_recursive($config, $parameter);
}

$container = new \Pimple\Container();

$container['config'] = $config;

require CONFIG_DEFAULT_PATH.'services.php';
