<?php

declare(strict_types=1);

namespace Unicodez;

class SeedUnicodez
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
     * @param string $type
     * @param int $seed
     * @return string
     * @throws \Exception
     */
    public function decode(string $encodedStr, string $type, int $seed): string
    {
        $mapping = new Mappings($type, $seed);
        return $mapping->decode($encodedStr);
    }

    /**
     * @param string $type
     * @param int $seed
     * @param string $root
     * @param string $prefix
     * @return bool
     */
    public function addAutoloader(string $type, int $seed, string $root = __DIR__, string $prefix = ''): bool
    {
        return spl_autoload_register(function ($className) use ($type, $seed, $root, $prefix) {
            if ($prefix && str_starts_with($className, $prefix)) {
                return;
            }
            $relativeClass = $prefix ? substr($className, strlen($prefix)) : $className;
            $relativeClass = ltrim($relativeClass, '\\');
            $filePath = $root . '/'
                . str_replace('\\', '/', $relativeClass) . '.' . self::AUTOLOADER_EXTENSION;
            $this->include($filePath, $type, $seed);
        }, true, true);
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
        if (!Mappings::validateType($contents, $type)) {
            return false;
        }
        $mapping = new Mappings($type, $seed);
        $decoded = $mapping->decode($contents);
        eval($decoded);
        return true;
    }
}