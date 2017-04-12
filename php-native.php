#!/usr/local/bin/php
<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

// Run map/reduce using forked PHP processes
// for local testing

$workers = $argv[1] ?? 10;
$dir = $argv[2] ?? __DIR__;

try {
    $server = new \Enron\Worker\Server((int)$workers, (bool)$workers >= 0);
    $server->start($dir);
} catch (\RuntimeException $e) {
    echo $e->getMessage() . "\n";
    exit;
}

$result = new \Enron\Result\Result($server->getReducer());
echo $result->getResultText();

