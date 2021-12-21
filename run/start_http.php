<?php

declare(strict_types=1);

$host = "0.0.0.0";
$port = 80;
$workerNum = 1;
$daemon = 0;

echo <<<EOT

Examples

Chat Room         http://127.0.0.1:{$port}/index.html

EOT;

$server = new Swoole\Http\Server($host, $port, SWOOLE_BASE);

$server->set([
    'worker_num' => $workerNum,
    'daemonize' => $daemon,
    'document_root' => __DIR__ . '/public',
    'enable_static_handler' => true,
    "static_handler_locations" => ['/', '/public/'],
]);

$fileContent = file_get_contents(__DIR__.'/public/index.html');

$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($fileContent) {
    $response->end($fileContent);
});

$server->start();