<?php
declare(strict_types = 1);

namespace Enron\Worker;

use Enron\Map\ZipReader;
use Enron\Reduce\Reducer;
use \React\EventLoop\Factory;
use \React\ChildProcess\Process;
use React\EventLoop\Timer\Timer;

class Server
{
    /** @var int */
    protected $numWorkers = 0;

    /** @var int */
    protected $maxWorkers = 0;

    /** @var Reducer */
    private $reducer;

    /** @var bool */
    private $forkable = false;

    /** @var array */
    private $pool = [];

    /** @var int */
    private $nextWorker = 0;

    public function __construct(int $maxWorkers = 10, $forkable = false)
    {
        $this->maxWorkers = $maxWorkers;
        $this->reducer = new Reducer;
        $this->forkable = $forkable;
    }

    private function getProcess($loop): \React\ChildProcess\Process
    {
        // Round robin
        if (count($this->pool) == $this->maxWorkers) {
            $worker = $this->pool[$this->nextWorker];
            $this->nextWorker = ++$this->nextWorker % $this->maxWorkers;
            return $worker;
        }
        ++$this->numWorkers;
        $process = new \React\ChildProcess\Process('php ./map.php');
        $process->start($loop);

        $this->pool[] = $process;

        return $process;
    }

    public function start(string $dir)
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException('Bad directory');
        }
        $loop = Factory::create();
        $iter = new \DirectoryIterator($dir);
        $reader = new ZipReader('/tmp/enron-test', new \ZipArchive());
        while (true) {
            $file = $iter->current();
            if ($file->isFile() && 'zip' == $file->getExtension()) {
                printf("Processing %s\n", $file->getRealPath());
                $reader->extract($file);
                $parser = $reader->getXmlParser();
                $emails = $parser->getEmails();
                if (0 < $this->maxWorkers) {
                    $loop->addPeriodicTimer(0.001, function (Timer $timer) use (&$emails) {
                        if (0 >= count($emails)) {
                            $timer->cancel();
                            $timer->getLoop()->stop();
                            return;
                        }
                        $email = array_pop($emails);
                        $process = $this->getProcess($timer->getLoop());
                        $this->fork($process, $email);
                    });
                } else {
                    foreach ($emails as $email) {
                        $worker = new \Enron\Worker\Worker($email);
                        $collection = $worker->run();
                        $this->reducer->reduce($collection);
                    }
                }
                $loop->run();
            }

            $iter->next();

            if (!$iter->valid()) {
                print("Done\n");
                break;
            }
        }
    }

    private function fork(Process $process, \SimpleXMLElement $node)
    {
        $process->stdin->write(preg_replace('#\s+#', ' ', $node->saveXML()));
        $process->stdout->on('data', function ($chunk) use ($process, $node) {
            $collection = json_decode($chunk, true);
            if (!json_last_error()) {
                $this->reducer->reduce($collection);
            } else {
                echo json_last_error_msg();
            }
        });

        $process->on('exit', function ($exitCode, $termSignal) {
            --$this->numWorkers;
        });
    }

    public function getReducer(): Reducer
    {
        return $this->reducer;
    }
}