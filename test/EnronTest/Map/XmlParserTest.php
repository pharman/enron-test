<?php
declare(strict_types = 1);

namespace Testing\EnronTest\Map;

use Enron\Map\XmlParser;

class XmlParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var XmlParser */
    private $parser;

    public function setUp()
    {
        $this->parser = new XmlParser($this->getXml());
    }

    public function testGetEmails()
    {
        $emails = $this->parser->getEmails();
        $this->assertEquals(1, count($emails));
    }

    public function testGetFile()
    {
        $files = $this->parser->getEmails();
        $this->assertEquals(1, count($files));
    }

    private function getXml(): \SimpleXMLElement
    {
        $fileName = basename(__FILE__);
        $dirName = dirname(__FILE__);
        $xml = <<<XML
<Root>
    <Batch>
        <Documents>
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
        </Documents>
    </Batch>
</Root>
XML;
        return new \SimpleXMLElement($xml);
    }
}