#!/usr/local/bin/php
<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

$reducer = new \Enron\Reduce\Reducer();
$total = $count = 0;
while (($data = fgets(STDIN)) !== false) {
    $data = explode("\t", $data);
    $total += (int)$data[1] ?? 0;
    ++$count;
}

printf("avg\t%d", round($total / $count, 2));
