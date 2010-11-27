<?php
class Kuniklo_Test_Suite {
    private $kuniklo, $test_cases_path, $count, $log;

    public function __construct($test_cases_path = ".") {
        set_error_handler(array($this, "handleError"));
        $this->kuniklo = Kuniklo_Markup::getInstance();
        $this->test_cases_path = $test_cases_path;
    }

    public function handleError($errno, $errstr, $errfile, $errline ) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public function run() {
        $this->log = array();

        $this->count = array(
            "total"     => 0,
            "passed"    => 0,
            "failed"    => 0,
            "error"     => 0,
        );

        foreach (glob(realpath($this->test_cases_path) . DIRECTORY_SEPARATOR . "*.xml") as $xml_file_name) {
            $xml = simplexml_load_file($xml_file_name);
            foreach ($xml->test as $test) {
                $log_entry = array(
                    "name"      => $test->title,
                    "result"    => "error", // fallback
                );

                try {
                    $source = $test->source;
                    $expected_result = $test->document->asXml();
                    $actual_result = $this->kuniklo->toXml($source);
                    if ($this->normalizeXml($expected_result) === $this->normalizeXml($actual_result)) {
                        $log_entry["result"] = "passed";
                    } else {
                        $log_entry["result"] = "failed";
                        $log_entry["expected_result"] = $expected_result;
                        $log_entry["actual_result"] = $actual_result;
                    }
                } catch (Exception $e) {
                    $log_entry["result"] = "error";
                    $log_entry["exception"] = $e;
                }

                ++$this->count[$log_entry["result"]];
                ++$this->count["total"];

                $this->log []= $log_entry;

                switch ($log_entry["result"]) {
                    case "passed":
                        print ".";
                        break;
                    case "failed":
                        print "F";
                        break;
                    case "error":
                        print "E";
                        break;
                }
            }
        }

        print PHP_EOL;
    }

    public function printResults() {
        print PHP_EOL . "{$this->count['total']} total, {$this->count['passed']} passed, {$this->count['failed']} failed, {$this->count['error']} error" . PHP_EOL;

        if ($this->count["failed"] > 0) {
            print PHP_EOL . "Failed tests:" . PHP_EOL;
            foreach ($this->log as $log_entry) {
                if ("failed" == $log_entry["result"]) {
                    print PHP_EOL . "==================================================================" . PHP_EOL . $log_entry["name"] . PHP_EOL;

                    print PHP_EOL . "Expected:" . PHP_EOL;
                    print $log_entry["expected_result"];
                    print PHP_EOL;

                    print PHP_EOL . "Actual:" . PHP_EOL;
                    print $log_entry["actual_result"];
                    print PHP_EOL;
                }
            }
        }

        if ($this->count["error"] > 0) {
            print PHP_EOL . "Errors:" . PHP_EOL;
            foreach ($this->log as $log_entry) {
                if ("error" == $log_entry["result"]) {
                    print PHP_EOL . "==================================================================" . PHP_EOL . $log_entry["name"] . PHP_EOL;
                    if (!empty($log_entry["exception"])) {
                        print get_class($log_entry["exception"]) . PHP_EOL;
                        print $log_entry["exception"]->getMessage() . PHP_EOL;
                        print $log_entry["exception"]->getFile() . ", line " . $log_entry["exception"]->getLine() . PHP_EOL;
                    }
                }
            }
        }
    }

    private function normalizeXml($xml) {
        $normalizer = new Kuniklo_Xml_Normalizer($xml);
        return (string)$normalizer;
    }
}