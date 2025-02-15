<?php

declare(strict_types=1);

use Unicodez\CLI;

if (php_sapi_name() != 'cli') {
    die('Must run from command line.');
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
ini_set('html_errors', 0);

require_once __DIR__ . '/vendor/autoload.php';

$strict = in_array('--strict', $_SERVER['argv']);
$arguments = new \cli\Arguments(compact('strict'));

$cli = new CLI(__FILE__, $arguments);
$cli->addOptionsAndFlags();

$arguments->parse();

if ($cli->is('quiet') && $cli->is('verbose')) {
    // you can't have both quiet and verbose, however since quiet is selected honour that!
    exit(1);
}

if (!$cli->is('quiet')) {
    echo PHP_EOL . CLI::generateBanner() . PHP_EOL . PHP_EOL;
}

try {
    if ($cli->command('version')) {
        $composerContents = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "composer.json"), true);
        $cli->out("Version: " . $composerContents['version']);
    } elseif ($cli->command('help')) {
        echo $arguments->getHelpScreen() . PHP_EOL;
    } elseif ($cli->command('status')) {
        $cli->runStatus();
    } elseif ($cli->command('encode')) {
        $cli->runEncode();
    } elseif ($cli->command('decode')) {
        $cli->runDecode();
    } elseif ($cli->command('cache-clear')) {
        $cli->runCacheClear();
    } else {
        $cli->out("No command provided.");
        $cli->out("Type `./unicodez help` for help.");
    }
    if (!$cli->is('quiet')) {
        echo PHP_EOL;
    }
    exit(0);
} catch (\Exception $e) {
    $cli->displayException($e);
    exit($e->getCode() ?: 1);
}
