#!/usr/local/bin/php
<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

ini_set('display_errors', 'off');

$loop = React\EventLoop\Factory::create();
$read = new \React\Stream\Stream(STDIN, $loop);

$read->on('data', function ($data) use ($loop) {
    $lines = explode("\n", $data);
    foreach ($lines as $line) {
        try {
            $node = new \SimpleXMLElement(trim($line));
            $worker = new \Enron\Worker\Worker($node);
            $collection = $worker->run();
            printf("length\t%d\n", $collection['count']);
        } catch (\Exception $e) {

        }
    }
});

$loop->run();