<?php

declare(strict_types=1);

namespace Unicodez;

use cli\Arguments;

/**
 * cli definition, commands and helpers
 */
class CLI
{
    public const array FLAVOURS = [
        'seed' => SeedUnicodez::class,
        'shebang' => ShebangUnicodez::class,
    ];

    private string $baseDir;

    private string $cliFile;

    private Arguments $arguments;

    public const array COMMANDS = ['help', 'version', 'status', 'encode', 'decode', 'cache-clear'];

    /**
     * @param string $cliFile pass in __FILE__
     * @param Arguments $arguments cli object
     */
    public function __construct(string $cliFile, Arguments $arguments)
    {
        $this->baseDir = dirname($cliFile);
        $this->cliFile = $cliFile;
        $this->arguments = $arguments;
    }

    /**
     * setup CLI arguments
     * @return void
     */
    public function addOptionsAndFlags(): void
    {
        $this->arguments->addFlag(['version', 'v'], "Display the version");
        $this->arguments->addFlag(['status'], "Display the status of the target(s)");
        $this->arguments->addFlag(['encode'], "Encode the target(s)");
        $this->arguments->addFlag(['decode'], "Decode the target(s)");
        $this->arguments->addFlag(['help', 'h'], 'Show this help screen');
        $this->arguments->addFlag(['cache-clear'], 'Clear the unicodez cache');

        $this->arguments->addFlag(['verbose'], ['description' => "Turn on verbose output", 'default' => false]);
        $this->arguments->addFlag(['quiet', 'q'], ['description' => "Disable all output", 'default' => false]);
        $this->arguments->addFlag(['recursive', 'r'], ['description' => "Recursively encode/decode files", 'default' => false]);
        $this->arguments->addFlag(['write', 'w'], ['description' => "Write output to files", 'default' => false]);
        $this->arguments->addFlag(['nobackup'], ['description' => "Don't Backup files if writing", 'default' => false]);

        $this->arguments->addOption(['target','t'], ['description' => "Target file/directory", 'default' => $this->baseDir]);
        $this->arguments->addOption(['flavour', 'f'], "Type : 'seed' or 'shebang'");
        $this->arguments->addOption(['map','m'], "Mapping to use for encoding/decoding");
        $this->arguments->addOption(['seed','s'], "Seed to use for encoding/decoding");
        $this->arguments->addOption(['glob'], "Use a glob style file filter");
        $this->arguments->addOption(['regex'], "Use a regex style style filter");
    }

    /**
     * was this command selected
     * @param $command
     * @return bool
     */
    public function command($command): bool
    {
        return ($this->arguments[$command] && in_array($command, self::COMMANDS));
    }

    /**
     * run the status command
     * @return bool
     * @throws \Exception
     */
    public function runStatus(): bool
    {
        if ($this->arguments['verbose'] && !$this->arguments['quiet']) {
            $this->out("status : " . $this->arguments['target']);
        }
        $files = $this->getFiles();
        if ($files === null) {
            return false;
        }

        $hasFiles = false;
        foreach ($files as $file) {
            if ($file->isEncoded()) {
                 $this->out("%C" . $file->getFilePath() . " : %G" . $file->getSummary());
                $hasFiles = true;
            } elseif ($file->isPHP()) {
                $this->out("%C" . $file->getFilePath() . " : %G" . $file->getSummary());
                $hasFiles = true;
            }
            else {
                $this->out("%C" . $file->getFilePath() . " : %G" . $file->getSummary());
            }
        }
        if (!$hasFiles) {
            $this->out("No target files found.");
        }
        return true;
    }

    /**
     * run the encode command
     * @return bool
     * @throws \Exception
     */
    public function runEncode(): bool
    {
        if ($this->arguments['verbose'] && !$this->arguments['quiet']) {
            $this->out("encode : %C" . $this->arguments['target']);
        }

        $files = $this->getFiles();
        if ($files === null) {
            return false;
        }
        $map = $this->arguments['map'];
        $seedStr = $this->arguments['seed'];
        $flavourStr = $this->arguments['flavour'];
        if (!$flavourStr) {
            if ($this->is('quiet')) {
                return false;
            }
            $flavourIdx = $this->menu(array_keys(self::FLAVOURS), null, "Choose a flavour");
            $flavourStr = array_keys(self::FLAVOURS)[$flavourIdx];
        }
        if (!array_key_exists($flavourStr, self::FLAVOURS)) {
            if (!$this->is('quiet')) {
                $this->out("%RUnrecognised flavour " . $flavourStr);
            }
            return false;
        }
        $flavour = self::FLAVOURS[$flavourStr];
        if (!$map) {
            if ($this->is("quiet")) {
                return false;
            }
            $mapIdx = $this->menu(Mappings::ALL_TYPES, null, "Choose a mapping");
            $map = Mappings::ALL_TYPES[$mapIdx];
        }
        if (!in_array($map, Mappings::ALL_TYPES)) {
            if (!$this->is("quit")) {
                $this->out("%RUnrecognised mapping " . $map);
            }
            return false;
        }
        if (!$seedStr) {
            $this->out("Enter a seed : ");
            $seedStr = $this->input("%i");
        }
        $seed = intval($seedStr);
        if (!$seed) {
            if (!$this->is("quiet")) {
                $this->out("%RInvalid seed : " . $seedStr);
            }
            return false;
        }
        if (!$this->is('quiet')) {
            $this->out("Encoding : %C" . $flavour . " : " . $map . " : " . $seed);
            if ($this->is("verbose")) {
                if ($this->is('nobackup')) {
                    $this->out("Not making backups.");
                }
            }
        }

        $unicodez = new $flavour();

        foreach ($files as $file) {
            if ($file->getFilePath() === $this->cliFile) {
                if (!$this->is('silent')) {
                    // probably not a good idea to encode the cli tool!
                    $this->out("Skipping CLI file : %C" . $file->getFilePath());
                }
            } elseif ($file->isPHP()) {
                try {
                    $this->out("Encoding file : %C" . $file->getFilePath());
                    $encoded = $unicodez->encode($file->getContents(), $map, $seed);
                    if ($this->is('write')) {
                        if ($this->is('verbose')) {
                            if ($this->is('verbose') && !$this->confirm("Overwrite file?", true)) {
                                continue;
                            }
                        }
                        if (!$this->is('nobackup')) {
                            $backupPath = $file->writeBackup();
                            if ($this->is('verbose')) {
                                $this->out("Wrote backup : %C" . $backupPath);
                            }
                        }
                        $this->out("Writing file : %C" . $file->getFilePath());
                        $file->writeContent($encoded);
                    } else {
                        $this->out("%C" . $encoded);
                    }
                } catch (\Exception $e) {
                    $this->displayException($e);
                }
            } else {
                if (!$this->is('silent')) {
                    $this->out("Skipping encoded file : %C" . $file->getFilePath());
                }
            }
        }
        return true;
    }

    /**
     * run the decode command
     * @return bool
     * @throws \Exception
     */
    public function runDecode(): bool
    {
        if ($this->arguments['verbose'] && !$this->arguments['quiet']) {
            $this->out("decode : %C" . $this->arguments['target']);
        }
        $files = $this->getFiles();
        if ($files === null) {
            return false;
        }
        $seed = $this->arguments['seed'];
        $requiresSeed = false;
        foreach ($files as $file) {
            if ($file->getFileType() === File::FILETYPE_ENCODED_SEED) {
                $requiresSeed = true;
            }
        }
        $promptEachSeed = false;
        if ($requiresSeed && !$seed) {
            if ($this->is("quiet")) {
                return false;
            }
            $this->out("At least 1 file requires a seed for decoding.");
            if ($this->confirm("Prompt for each seed?")) {
                $promptEachSeed = true;
            } else {
                $seed = $this->inputSeed();
                if (!$seed) {
                    return false;
                }
            }
        }
        $shebangUnicodez = null;
        $seedUnicodez = null;

        foreach ($files as $file) {
            if ($file->getFilePath() === $this->cliFile) {
                if (!$this->is('silent')) {
                    // probably not a good idea to encode the cli tool!
                    $this->out("Skipping CLI file : %C" . $file->getFilePath());
                }
                continue;
            } elseif ($file->isEncoded()) {
                try {
                    $this->out("Decoding file : %C" . $file->getFilePath());
                    if ($file->getFileType() === File::FILETYPE_ENCODED_SEED) {
                        if ($promptEachSeed) {
                            while (!$seed) {
                                $seed = $this->inputSeed();
                            }
                        }
                        if (!$seedUnicodez) {
                            $seedUnicodez = new \Unicodez\SeedUnicodez();
                        }
                        $decoded = $seedUnicodez->decode($file->getContents(), $seed);
                    } else {
                        if (!$shebangUnicodez) {
                            $shebangUnicodez = new \Unicodez\ShebangUnicodez();
                        }

                        $decoded = $shebangUnicodez->decode($file->getContents());
                    }
                    if ($this->is('write')) {
                        if ($this->is('verbose') && !$this->confirm("Overwrite file?", true)) {
                            continue;
                        }
                        $backupPath = $file->writeBackup();
                        if ($this->is('verbose')) {
                            $this->out("Wrote backup : %C" . $backupPath);
                        }
                        $this->out("Writing file : %C" . $file->getFilePath());
                        $file->writeContent($decoded->decoded);
                    } else {
                        $this->out("%C" . $decoded);
                    }
                } catch (\Exception $e) {
                    $this->displayException($e);
                }
            } else {
                if (!$this->is('silent')) {
                    $this->out("Skipping unencoded file : %C" . $file->getFilePath());
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function runCacheClear(): bool
    {
        $cache = new Cache();
        $cache->clear();
        return true;
    }

    /**
     * input a seed value
     * @return int|null
     * @throws \Exception
     */
    private function inputSeed(): ?int
    {
        $this->out("Enter a seed : ");
        $seedStr = $this->input("%i");
        $seed = intval($seedStr);
        if (!$seed) {
            $this->out("%RInvalid seed : " . $seedStr);
            return null;
        }
        return $seed;
    }

    /**
     * is a flag specified
     * @param string $name
     * @return bool
     */
    public function is(string $name): bool
    {
        if (!$this->arguments->isFlag($name)) {
            return false;
        }
        return $this->arguments[$name];
    }

    /**
     * cli out
     * @param string $content
     * @return void
     */
    public function out(string $content): void
    {
        if (!$this->is('quiet')) {
            \cli\out($content . '%n' . PHP_EOL);
        }
    }

    /**
     * cli err
     * @param string $content
     * @return void
     */
    public function err(string $content): void
    {
        if (!$this->is('quiet')) {
            \cli\err($content);
        }
    }

    /**
     * cli input
     * @param string $params
     * @return string
     * @throws \Exception
     */
    public function input(string $params): string
    {
        if ($this->is('quiet')) {
            throw new \Exception("Can't input when in quiet mode.");
        }
        return \cli\input($params);
    }

    /**
     * cli confirm
     * @param string $question
     * @param bool $default
     * @return bool
     * @throws \Exception
     */
    public function confirm(string $question, bool $default = false): bool
    {
        if ($this->is('quiet')) {
            throw new \Exception("Can't confirm when in quiet mode.");
        }
        return \cli\confirm($question, $default);
    }

    /**
     * cli menu
     * @param array $options
     * @param string|null $default
     * @param string $text
     * @return string
     * @throws \Exception
     */
    public function menu(array $options, ?string $default, string $text): string
    {
        if ($this->is('quiet')) {
            throw new \Exception("Can't select menu item when in quiet mode.");
        }
        return \cli\menu($options, $default, $text);
    }

    /**
     * get a list of File objects
     * @return File[]|null
     * @throws \Exception
     */
    private function getFiles(): ?array
    {
        $filePaths = is_file($this->arguments['target'])
            ? [$this->arguments['target']]
            : CLI::fileEnumerator(
                $this->arguments['target'],
                $this->arguments['glob'],
                $this->arguments['regex'],
                $this->arguments['recursive']
            );
        if ($this->arguments['verbose']) {
            foreach ($filePaths as $filePath) {
                $this->out(" Target File : %C" . $filePath);
            }
            if (!$this->confirm("Use these targets candidates?", true)) {
                return null;
            }
        }
        $files = [];
        foreach ($filePaths as $filePath) {
            $files[] = new File($filePath);
        }
        return $files;
    }

    /**
     * display an exception
     * @param \Exception $exc
     * @return void
     */
    public function displayException(\Exception $exc): void
    {
        if ($this->is('verbose')) {
            \cli\err("%R" . $exc . "%n" . PHP_EOL);
        } elseif (!$this->is('quiet')) {
            \cli\err(
                "Error: %R" . $exc->getMessage() . "%n " .
                "in %C" . $exc->getFile() . "%n on line %C" . $exc->getLine() . "%n" . PHP_EOL
            );
        }
    }

    /**
     * generate the unicodez cli banner
     * @return string
     */
    public static function generateBanner(): string
    {
        $banner = <<<BANNER
*   *  *    *  ***   ****   ***   ***    *****  *****
*   *  **   *   *   *      *   *  *  *   *         *
*   *  *  * *   *   *      *   *  *   *  ***      *
*   *  *   **   *   *      *   *  *  *   *       *
 ***   *    *  ***   ****   ***   ***    *****  *****
BANNER;
        $sets = [ Mappings::TYPE_BRAILLE, Mappings::TYPE_RUNIC, Mappings::TYPE_DICE,
            Mappings::TYPE_CARD_SUITES, Mappings::TYPE_CHESS, Mappings::TYPE_PLANETS ];
        $type = $sets[array_rand($sets)];
        list($from, $to) = Mappings::getUnicodeRange($type);
        $charsToUse = Mappings::generateUnicodeSet($from, $to);
        while (($pos = strpos($banner, "*")) !== false) {
            $char = $charsToUse[array_rand($charsToUse)];
            $banner = substr_replace($banner, $char, $pos, 1);
        }
        return $banner;
    }

    /**
     * enumerate files
     * 🏆awarded by ChatGPT : "Finalest Ultimate Definitive Finalization" revision
     * @param string|null $path
     * @param string|null $globPattern
     * @param string|null $regexPattern
     * @param bool $recursive
     * @return array
     */
    public static function fileEnumerator(
        ?string $path = null,
        ?string $globPattern = null,
        ?string $regexPattern = null,
        bool $recursive = false
    ): array {
        if ($globPattern && $path) {
            throw new \InvalidArgumentException(
                "Cannot specify both 'globPattern' and 'path'. Choose one."
            );
        } elseif ($globPattern && $recursive) {
            throw new \InvalidArgumentException(
                "Cannot specify both 'globPattern' and 'recursive'. Glob is inherently recursive!"
            );
        }

        $files = [];

        // if globPattern is specified, apply it relative to CWD
        if ($globPattern) {
            foreach (glob(getcwd() . DIRECTORY_SEPARATOR . $globPattern) as $filePath) {
                if (is_file($filePath) && (!$regexPattern || preg_match($regexPattern, $filePath))) {
                    $files[] = $filePath;
                }
            }
            return $files;
        }

        // ensure path is valid if globPattern isn't used
        if (!$path || !is_dir($path)) {
            throw new \InvalidArgumentException("Invalid directory specified: '$path'.");
        }

        $directoryIterator = new \RecursiveDirectoryIterator(
            $path,
            \FilesystemIterator::SKIP_DOTS // Ignore "." and ".."
        );

        $iterator = $recursive
            ? new \RecursiveIteratorIterator($directoryIterator)
            : new \IteratorIterator($directoryIterator);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getPathname();

                // apply regex filter if one is specified
                if ($regexPattern && !preg_match($regexPattern, $filePath)) {
                    continue;
                }

                $files[] = $filePath;
            }
        }
        return $files;
    }
}
