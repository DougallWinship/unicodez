<?php
require_once('layout-prepare.php');

$title = "Unicodez : Seed Autoload Test";

$unicodez = new \Unicodez\SeedUnicodez();
$unicodez->addAutoloader(\Unicodez\Mappings::TYPE_RUNIC, 1, dirname(__DIR__) . '/src');

$runicTest = new \Runic\SeedRunicTest();
$runicTest->showAllSets();

require_once('layout-render.php');