#!/usr/local/bin/php
<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

use Enron\Map\ZipReader;

$dir = $argv[1] ?? null;
$mapper = $argv[2] ?? null;
$reducer = $argv[3] ?? null;
if (!$dir || !is_dir($dir)) {
    die('Invalid directory');
}

$iter = new DirectoryIterator($dir);
$tmpDir = '/tmp/enron-temp';

$hadoop = '$HADOOP_HOME/bin/hadoop';
$streaming = 'jar $HADOOP_HOME/share/hadoop/tools/lib/hadoop-streaming-2.8.0.jar';
foreach ($iter as $file) {
    if ($file->isFile() && 'zip' == $file->getExtension()) {
        $reader = new ZipReader($tmpDir . DIRECTORY_SEPARATOR . $file->getFilename(), new \ZipArchive());
        $reader->extract($file);
        $xmlFile = $reader->getXmlManifest()->getPathname();
        $xml = file_put_contents($xmlFile, preg_replace('#\s+#', ' ', file_get_contents($xmlFile)));
    }
}

$cmd = <<<CMD
$hadoop $streaming \
-inputreader "StreamXmlRecordReader,begin=<Document ,end=</Document>" \
-input $tmpDir/*/*.xml \
-output /tmp/hadoop-test \
-mapper $mapper \
-reducer $reducer \
CMD;

shell_exec($cmd);

system('rm -rf ' . $tmpDir);