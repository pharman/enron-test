<?php
declare(strict_types = 1);

namespace Testing\EnronTest\Reduce;

use Enron\Reduce\Reducer;

class ReducerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Reducer */
    private $reducer;

    public function setUp()
    {
        $this->reducer = new Reducer();
    }

    public function testReduce()
    {
        $data = ['to' => ['to'], 'cc' => ['cc'], 'count' => 1];
        $this->reducer->reduce($data);
        $this->assertEquals(1.0, $this->reducer->getAverageWordCount());
        $this->assertEquals(['to' => 1.0, 'cc' => 0.5], $this->reducer->getTopRecipients());
        $data = ['to' => ['to'], 'cc' => ['cc'], 'count' => 2];
        $this->reducer->reduce($data);
        $this->assertEquals(1.5, $this->reducer->getAverageWordCount());
        $this->assertEquals(['to' => 2.0, 'cc' => 1.0], $this->reducer->getTopRecipients());
    }

    public function testTopRecipients()
    {
        $maxEmails = 6000;
        $maxRecps = 100;
        $expectedHighestCount = $maxEmails / $maxRecps + 1;
        foreach (range(0, $maxEmails) as $recp) {
            $recipient = $recp % $maxRecps;
            $data[] = ['to' => ["to-$recipient"], 'cc' => ["cc-$recipient"], 'count' => $recp];
        }

        foreach ($data as $mail) {
            $this->reducer->reduce($mail);
        }

        $reduced = $this->reducer->getTopRecipients();

        $this->assertEquals(100, count($reduced));
        $this->assertEquals($expectedHighestCount, array_shift($reduced));
    }
}