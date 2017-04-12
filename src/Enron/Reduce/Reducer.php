<?php
declare(strict_types = 1);

namespace Enron\Reduce;

class Reducer
{
    /** @var int */
    private $numEmails = 0;

    /** @var array */
    private $topRecp = [];

    /** @var int */
    private $totalWordCount = 0;

    public function reduce(array $data): bool
    {
        if (!count($data)) {
            return false;
        }
        ++$this->numEmails;

        if (isset($data['to'])) {
            $this->addRecipients($data['to'], 1.0);
        }
        if (isset($data['cc'])) {
            $this->addRecipients($data['cc'], .5);
        }

        if (isset($data['count'])) {
            $this->totalWordCount += (int)$data['count'];
        }

        return true;
    }

    private function addRecipients(array $recp, $score) {
        foreach ($recp as $recipient) {
            if (isset($this->topRecp[$recipient])) {
                $this->topRecp[$recipient] += $score;
            } else {
                $this->topRecp[$recipient] = $score;
            }
        }
    }

    public function getTopRecipients(): array
    {
        arsort($this->topRecp);
        return array_slice($this->topRecp, 0, 100, true);
    }

    public function getAverageWordCount(): float
    {
        if ($this->totalWordCount > 0) {
            return round($this->totalWordCount / $this->numEmails, 2);
        }

        return 0;
    }
}