<?php

declare(strict_types=1);

namespace Unicodez;

abstract class Unicodez
{
    public const string AUTOLOADER_EXTENSION = "php";

    /**
     * @param callable $callback
     * @param string|null $type
     * @param int|null $seed
     * @param string $root
     * @param string $prefix
     * @return bool
     */
    protected function registerAutoloader(
        callable $callback,
        string $type = null,
        int $seed = null,
        string $root = __DIR__,
        string $prefix = ''
    ): bool {
        return spl_autoload_register(function ($className) use ($callback, $type, $seed, $root, $prefix) {
            if ($prefix && str_starts_with($className, $prefix)) {
                return;
            }
            $relativeClass = $prefix ? substr($className, strlen($prefix)) : $className;
            $relativeClass = ltrim($relativeClass, '\\');
            $filePath = $root . '/'
                . str_replace('\\', '/', $relativeClass) . '.' . self::AUTOLOADER_EXTENSION;
            $callback($filePath, $type, $seed);
        }, true, true);
    }

    /**
     * @param string $filePath
     * @return string|null
     */
    protected function readUTF8File(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }
        $contents = file_get_contents($filePath);
        $detected =  mb_detect_encoding($contents, "UTF-8, ISO-8859-1", true);
        if ($detected != "UTF-8") {
            $contents = mb_convert_encoding($contents, "UTF-8", $detected);
        }
        return $contents;
    }
}
