<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require dirname(__DIR__) . '/vendor/autoload.php';

$unicodez = new \Unicodez\SeedUnicodez();
$unicodez->addAutoloader(\Unicodez\Mappings::TYPE_RUNIC, 1, dirname(__DIR__) . '/src');

$runicTest = new \Runic\SeedRunicTest();
$runicTest->showAllSets();