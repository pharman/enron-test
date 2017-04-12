<?php
declare(strict_types = 1);

namespace Testing\EnronTest\Map;

use Enron\Map\ZipReader;

class ZipReaderTest extends \PHPUnit\Framework\TestCase
{
    /** @var ZipReader */
    private $zipReader;

    public function setUp()
    {
        $this->zipReader = new ZipReader('/tmp/' . uniqid(), new \ZipArchive());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExtract()
    {
        $this->zipReader->extract(new \SplFileInfo(__FILE__));
    }
}