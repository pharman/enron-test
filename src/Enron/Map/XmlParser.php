<?php
declare(strict_types = 1);

namespace Enron\Map;

class XmlParser
{
    /** @var \SimpleXMLElement $simpleXml */
    private $simpleXml;

    public function __construct(\SimpleXMLElement $simpleXMLElement)
    {
        $this->simpleXml = $simpleXMLElement;
    }

    public function getEmails(): array
    {
        return $this->simpleXml->xpath('/Root/Batch/Documents/Document');
    }

    public function getFile(\SimpleXMLElement $node): array
    {
        return $node->xpath('Files/File[@FileType="Text"]/ExternalFile[@FileName and @FilePath]');
    }
}