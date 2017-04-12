<?php
declare(strict_types = 1);

namespace Enron\Map;

class Collector
{
    public function getStats(\SimpleXMLElement $node, string $tmpDir): array
    {
        list($to, $cc) = $this->getRecipients($node);
        $count = $this->getWordCount($node, $tmpDir);

        if (!$to && !$cc && !$count) return [];

        return ['to' => $to, 'cc' => $cc, 'count' => $count];
    }

    private function getRecipients(\SimpleXMLElement $node): array
    {
        $to = $cc = [];
        $recpTag = $node->xpath('Tags/Tag[@TagName="#To" and @TagValue]');
        if (count($recpTag)) {
            $to = explode('>, ', (string)$recpTag[0]->attributes()['TagValue']);
        }

        $recpTag = $node->xpath('Tags/Tag[@TagName="#CC" and @TagValue]');
        if (count($recpTag)) {
            $cc = explode('>, ', (string)$recpTag[0]->attributes()['TagValue']);
        }

        return [$to, $cc];
    }

    private function getWordCount(\SimpleXMLElement $node, string $tmpDir): int
    {
        $file = $node->xpath('Files/File[@FileType="Text"]/ExternalFile[@FileName and @FilePath]');
        if (count($file)) {
            $fileAttrib = $file[0]->attributes();
            $filePath = $tmpDir . DIRECTORY_SEPARATOR . (string)$fileAttrib['FilePath'] . DIRECTORY_SEPARATOR .  (string)$fileAttrib['FileName'];
            if (file_exists($filePath)) {
                $fileData = file($filePath, FILE_IGNORE_NEW_LINES);
                $parser = new MailParser($fileData);
                return str_word_count($parser->getBody());
            }
        }

        return 0;
    }
}