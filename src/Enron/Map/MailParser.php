<?php
declare(strict_types=1);

namespace Enron\Map;

class MailParser
{
    /** @var array */
    private $mailLines;

    public function __construct(array $mailLines)
    {
        $this->mailLines = $mailLines;
    }

    public function getBody(): string
    {
        $mailLines = $this->mailLines;
        foreach ($mailLines as $line) {
            if (preg_match('#\w+:\s#', $line)) {
                array_shift($mailLines);
            } else {
                break;
            }
        }

        return implode('', $mailLines);
    }
}