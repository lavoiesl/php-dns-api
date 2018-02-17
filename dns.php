<?php

require "vendor/autoload.php";

function output($array) {
    header('Content-Type: application/json');
    echo json_encode($array);
    exit();
}

$loop = React\EventLoop\Factory::create();
$factory = new React\Dns\Resolver\Factory();

$hostname = filter_input(INPUT_GET, "hostname", FILTER_SANITIZE_STRING);
if (!$hostname) {
    header("HTTP/1.0 400 Bad Request");
    output(['error' => "Invalid 'hostname' parameter"]);
}

$nameserver = filter_input(INPUT_GET, "nameserver", FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
if (!$nameserver) {
    header("HTTP/1.0 400 Bad Request");
    output(['error' => "Invalid 'nameserver' parameter"]);
}

$dns = $factory->create($nameserver, $loop);
$dns->resolve($hostname)
    ->then(function ($ip) {
        output(['ip' => $ip]);
    })
    ->otherwise(function(React\Dns\Query\TimeoutException $e) {
        header("HTTP/1.0 504 Gateway timeout");
        output(['error' => $e->getMessage()]);
    })
    ->otherwise(function(React\Dns\BadServerException $e) {
        header("HTTP/1.0 502 Bad gateway");
        output(['error' => $e->getMessage()]);
    })
    ->otherwise(function(React\Dns\RecordNotFoundException $e) {
        header("HTTP/1.0 404 Not Found");
        output(['error' => $e->getMessage()]);
    })
    ->otherwise(function($e) {
        header("HTTP/1.0 500 Internal server error");
        output(['error' => $e->getMessage()]);
    });

$loop->run();
