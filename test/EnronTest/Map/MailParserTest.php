<?php
declare(strict_types = 1);

namespace Testing\EnronTest\Map;

use Enron\Map\MailParser;

class MailParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var MailParser */
    private $mail;

    public function setUp()
    {
        $mailLines = <<<MAIL
To: Testing
Subject: Testing

Test body
MAIL;

        $this->mail = new MailParser(explode("\n", $mailLines));
    }

    public function testGetBody()
    {
        $this->assertEquals('Test body', $this->mail->getBody());
    }
}