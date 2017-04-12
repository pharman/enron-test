<?php
declare(strict_types = 1);

namespace Enron\Worker;

class Worker
{
    /** @var \SimpleXMLElement */
    private $xml;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function run(): array
    {
        $tmpDir = '/tmp/enron-test';
        $collector = new \Enron\Map\Collector();
        return $collector->getStats($this->xml, $tmpDir);
    }
}