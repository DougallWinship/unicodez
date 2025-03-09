<?php
require_once("layout-prepare.php");

$title = "Unicodez : Shebang Autoloader Test";

$unicodez = new \Unicodez\ShebangUnicodez();
$unicodez->addAutoloader(dirname(__DIR__) . '/src');

$runicTest = new \Runic\ShebangRunicTest();
$runicTest->showAllSets();

require_once("layout-render.php");