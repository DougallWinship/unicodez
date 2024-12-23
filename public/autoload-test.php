<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require dirname(__DIR__) . '/vendor/autoload.php';

$unicoder = new \Unicoder\Unicoder(Unicoder\Unicoder::TYPE_FLAGS);
$unicoder->addAutoloader(dirname(__DIR__) . '/src');

$flagTest = new \Flag\FlagTest();
$flagTest->showAllSets();