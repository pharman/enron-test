<?php
declare(strict_types = 1);

namespace Testing\EnronTest\Reduce;

use Enron\Worker\Worker;

class WorkerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Worker */
    private $worker;

    public function setUp()
    {
        $this->worker = new Worker($this->getXml());
    }

    public function testRun()
    {
        $collected = $this->worker->run();
        $this->assertEquals([
            'to' => ['to'],
            'cc' => ['cc'],
            'count' => 0
        ], $collected);
    }

    private function getXml(): \SimpleXMLElement
    {
        $fileName = basename(__FILE__);
        $dirName = dirname(__FILE__);
        $xml = <<<XML
<Document>
    <Tags>
        <Tag TagName="#To" TagValue="to"></Tag>
        <Tag TagName="#CC" TagValue="cc"></Tag>
    </Tags>
    <Files>
        <File FileType="Text">
            <ExternalFile FileName="$fileName" FilePath="$dirName"></ExternalFile>
        </File>
    </Files>
</Document>
XML;
        return new \SimpleXMLElement($xml);
    }
}