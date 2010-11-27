<?php
function __autoload($class_name) {
    require_once str_replace("_", DIRECTORY_SEPARATOR, $class_name) . ".php";
}

$kuniklo_test_suite = new Kuniklo_Test_Suite(
    realpath(empty($_SERVER["argv"][1]) ?
        (dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "tests")
        : $_SERVER["argv"][1])
);
$kuniklo_test_suite->run();
$kuniklo_test_suite->printResults();
