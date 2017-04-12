<?php
declare(strict_types=1);

namespace Enron\Map;

class ZipReader
{
    /** @var string */
    private $tmpDir;

    /** @var \ZipArchive */
    private $zipArchive;

    public function __construct(string $tmpDir, \ZipArchive $zipArchive)
    {
        $this->tmpDir = $tmpDir;
        $this->zipArchive = $zipArchive;
    }

    public function extract(\SplFileInfo $zip)
    {
        $canOpen = $this->zipArchive->open($zip->getRealPath());
        if ($canOpen !== true) {
            throw new \RuntimeException('Bad zip file');
        }
        system('rm -rf ' . $this->tmpDir);
        mkdir($this->tmpDir, 0777, true);
        $this->zipArchive->extractTo($this->tmpDir);
        $this->zipArchive->close();
    }

    public function getXmlParser(): XmlParser
    {
        $tmpIter = new \DirectoryIterator($this->tmpDir);
        foreach ($tmpIter as $tmp) {
            if ('xml' == $tmp->getExtension()) {
                return new XmlParser(simplexml_load_file($tmp->getRealPath()));
            }
        }
        throw new \RuntimeException('Missing XML manifest');
    }

    public function getXmlManifest(): \SplFileInfo
    {
        $tmpIter = new \DirectoryIterator($this->tmpDir);
        foreach ($tmpIter as $tmp) {
            if ('xml' == $tmp->getExtension()) {
                return $tmp;
            }
        }
        throw new \RuntimeException('Missing XML manifest');
    }
}