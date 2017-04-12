# Enron email corpus parsing

Code is an implementation of map/reduce in PHP.

It can be run using forked PHP processes for simple local execution. It is designed to be runnable in a parallel processing framework such as Hadoop, which is more appropriate given the size of the data.

# Goal
1. What is the average length, in words, of the emails? (Ignore attachments)
2. Which are the top 100 recipient email addresses? (An email sent to N recipients would could N times - count “cc” as 50%)

# Assumptions
All zip files must be at the root level of the specified directory.

Each zip file will have a single XML file containing the email manifest and a series of text files containing the email content.

Assumes that "average length, in words" is of the email body, excluding headers.

# Installation
Any non-PHP dependencies are out of scope

Assuming Composer installed, run:
```
composer install
composer dump-autoload -o
```
Ensure php-zip extension is enabled.

# Execution
There are several small php scripts in the root which act as the map/reduce handlers for both native PHP and Hadoop applications.

To run in forked processes, run:
```
./php-native.php <num-procs> <directory>
```
Where num-procs is greater than 0. The server will run <num-procs> instances of `map.php`

If 0 is specified then a single process will read the files and process the result.

The map/reduce pipeline can be run independently using map.php and reduce.php. This is roughly equivalent to how Hadoop would execute.
```
echo xml-doc | ./map.php | ./reduce.php
```

Running with Hadoop is a little more involved due to my limited knowledge of the environment.

### Calculate top 100 recipients
```
./hadoop.php ../enron-samples/multi/ ./map-names.php aggregate
hadoop fs -cat "/tmp/hadoop-test/part-*" | sort -n -k 2 -r -t $'\t' | head -n 100
```

### Calculate average word count
```
./hadoop.php ../enron-samples/ ./map-avg.php ./reduce-avg.php
hadoop fs -cat "/tmp/hadoop-test/part-*" | tail -n 1
```
Further investigation is needed into how HDFS works across multiple nodes.

# Tests
Ensure php-mbstring extension is enabled.

Run PHPUnit tests:

```
./vendor/bin/phpunit -c tests/ tests
```

# Notes
### Hadoop
It was difficult to use Hadoop to process the data. This is due to the data being gzipped and in XML. Hadoop has some rudimentary XML functionality but is quite limited. It was necessary to write a pre-processor to wrangle the data into something Hadoop can easily process. The script file `hadoop.php` tries to do this however is really just a proof of concept and needs refinement.

### Attempts with AWS EMR
AWS EMR is a hosted Hadoop cluster which could be used to process this data set. My attempts to do this were not succesful due to not being able to mount an EBS snapshot into EMR instances. It is possible to do this using S3 but I have not delved too deeply here.

In essence:

* Created EC2
* Mounted EBS vol
* Created S3 bucket
* Copied data to S3
* Create EMR cluster with master & 1 node
* Add script to deploy code to nodes
* Create streaming step with php scripts and S3 bucket