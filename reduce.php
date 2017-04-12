#!/usr/local/bin/php
<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

$reducer = new \Enron\Reduce\Reducer();
while (($data = fgets(STDIN)) !== false) {
    $collection = json_decode($data, true);
    if (!json_last_error()) {
        $reducer->reduce($collection);
    }
}

$result = new \Enron\Result\Result($reducer);
echo $result->getResultText();