<?php

declare(strict_types=1);

namespace Unicodez;

/**
 * unicodez using an explicit seed
 */
class SeedUnicodez extends Unicodez
{
    public const string AUTOLOADER_EXTENSION = "php";

    /**
     * @param string $input
     * @param string $type
     * @param int $seed
     * @return string
     * @throws \Exception
     */
    public function encode(string $input, string $type, int $seed): string
    {
        $mapping = new Mappings($type, $seed);
        return $mapping->encode($input);
    }

    /**
     * @param string $encodedStr
     * @param int $seed
     * @return Decoded
     * @throws \Exception
     */
    public function decode(string $encodedStr, int $seed): Decoded
    {
        $type = Mappings::sniffTypeSet($encodedStr);
        $mapping = new Mappings($type, $seed);
        return $mapping->decode($encodedStr);
    }

    /**
     * @param string $type
     * @param int $seed
     * @param string $root
     * @param string $prefix
     * @return bool
     * @throws \Exception
     */
    public function addAutoloader(string $type, int $seed, string $root = __DIR__, string $prefix = ''): bool
    {
        $callback = function ($filePath, $type, $seed) {
            $this->include($filePath, $type, $seed);
        };
        return parent::registerAutoloader($callback, $type, $seed, $root, $prefix);
    }

    /**
     * @param string $filePath
     * @param string $type
     * @param int $seed
     * @return bool
     * @throws \Exception
     */
    public function include(string $filePath, string $type, int $seed): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $contents = file_get_contents($filePath);
        $detected =  mb_detect_encoding($contents, "UTF-8, ISO-8859-1", true);
        if ($detected != "UTF-8") {
            $contents = mb_convert_encoding($contents, "UTF-8", $detected);
        }
        if (!Mappings::validateType($type, $contents)) {
            return false;
        }
        $mapping = new Mappings($type, $seed);
        $decoded = $mapping->decode($contents);
        eval($decoded);
        return true;
    }
}
