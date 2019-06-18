<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

//On production environments
error_reporting(0);
ini_set("memory_limit", "1000M");
function fatalErrorHandler() {
    $error = error_get_last();
    if ($error) {
        file_put_contents(__DIR__ . '/../var/log/sup-errors.log', json_encode($error), FILE_APPEND);
        header('HTTP/1.1 500 Internal Server Error');
        echo '<h1>Sorry the website just had an error</h1>Go <a href="#" onclick="window.history.back(); return false">Back</a>';
    }
}

# Registering shutdown function
register_shutdown_function('fatalErrorHandler');

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
