#!/usr/local/bin/php
<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$read = new \React\Stream\Stream(STDIN, $loop);
$read->on('data', function ($data) use ($loop) {
    try {
        $node = new \SimpleXMLElement(trim($data));
        $worker = new \Enron\Worker\Worker($node);
        $collection = $worker->run();
        echo json_encode($collection) . PHP_EOL;
    } catch (\Exception $e) {

    }
});

$loop->run();