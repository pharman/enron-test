<?php
declare(strict_types = 1);

namespace Enron\Result;

use Enron\Reduce\Reducer;

class Result
{
    /** @var Reducer */
    private $reducer;

    public function __construct(Reducer $reducer)
    {
        $this->reducer = $reducer;
    }

    public function getResultText(): string
    {
        ob_start();
        printf("Average word length: %d\n", $this->reducer->getAverageWordCount());
        $recp = $this->reducer->getTopRecipients();
        printf("Top 100 recp (%d):\n", count($recp));
        array_walk(
            $recp,
            function($count, $recipient) {
                printf("%s (%0.2f)\n", $recipient, $count);
            }
        );

        return ob_get_clean();
    }
}