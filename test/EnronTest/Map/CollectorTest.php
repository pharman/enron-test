<?php
declare(strict_types = 1);

namespace Testing\EnronTest\Map;

use Enron\Map\Collector;

class CollectorTest extends \PHPUnit\Framework\TestCase
{
    /** @var \EnronTest\Map\Collector */
    private $collector;

    public function setUp()
    {
        $this->collector = new Collector();
    }

    public function testGetStats()
    {
        $fileName = basename(__FILE__);
        $dirName = dirname(__FILE__);
        $xml = <<<XML
<test>
    <Tags>
        <Tag TagName="#To" TagValue="to"></Tag>
        <Tag TagName="#CC" TagValue="cc"></Tag>
    </Tags>
    <Files>
        <File FileType="Text">
            <ExternalFile FileName="$fileName" FilePath="$dirName"></ExternalFile>
        </File>
    </Files>
</test>
XML;

        $node = new \SimpleXMLElement($xml);
        $stats = $this->collector->getStats($node, '');
        $this->assertEquals(1, count($stats['to']));
        $this->assertEquals('to', $stats['to'][0]);
        $this->assertEquals(1, count($stats['cc']));
        $this->assertEquals('cc', $stats['cc'][0]);
        $this->assertTrue(0 < $stats['count']);
    }
}