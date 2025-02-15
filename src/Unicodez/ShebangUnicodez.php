<?php

declare(strict_types=1);

namespace Unicodez;

/**
 * unicodez using a pseudo-shebang
 */
class ShebangUnicodez extends Unicodez
{
    public const string SHEBANG_DELIMITER = "\u{FEFF}";

    /**
     * @param string $input
     * @param string $type
     * @param int $seed
     * @return string
     * @throws \Exception
     */
    public function encode(string $input, string $type, int $seed): string
    {
        $shebang = self::generateShebang($type, $seed);
        $mapping = new Mappings($type, $seed);
        $encoded = $mapping->encode($input);
        return $shebang . $encoded;
    }

    /**
     * @param string $encodedStr
     * @return Decoded
     * @throws \Exception
     */
    public function decode(string $encodedStr): Decoded
    {
        list($type, $seed, $content) = self::readShebang($encodedStr);
        $mapping = new Mappings($type, $seed);
        return $mapping->decode($content);
    }

    /**
     * @param string $input
     * @return array|null
     * @throws \Exception
     */
    public static function readShebang(string $input): ?array
    {
        // Find the position of the delimiter
        $position = mb_strpos($input, self::SHEBANG_DELIMITER, 0, 'UTF-8');

        if ($position === false) {
            throw new \Exception('Shebang Delimiter not found in input.');
        }

        $shebang = mb_substr($input, 0, $position, 'UTF-8');
        $content = mb_substr($input, $position + 1, null, 'UTF-8');
        $type = Mappings::sniffTypeSet($shebang);
        if (!$type) {
            return null;
        } elseif ($type === Mappings::TYPE_FLAGS) {
            $set = CountryCodes::getUnicodeSet();
            $chunkSize = 2;
        } else {
            $range = Mappings::getUnicodeRange($type);
            if (!$range) {
                throw new \Exception('Shebang Delimiter not found in input.');
            }
            $set = Mappings::generateUnicodeSet($range[0], $range[1]);
            $chunkSize = 1;
        }
        $base = count($set);
        $mapCharsToIndices = array_flip($set);
        $seed = 0;
        $length = mb_strlen($shebang, 'UTF-8');
        for ($idx = 0; $idx < $length; $idx += $chunkSize) {
            $char = mb_substr($shebang, $idx, $chunkSize, 'UTF-8');
            if (!isset($mapCharsToIndices[$char])) {
                throw new \Exception("Invalid character in shebang '{$char}'");
            }
            $seed = $seed * $base + $mapCharsToIndices[$char];
        }
        return [$type, $seed, $content];
    }

    /**
     * @param string $type
     * @param int $seed
     * @return string
     */
    private static function generateShebang(string $type, int $seed): string
    {
        if ($type === Mappings::TYPE_FLAGS) {
            $set = CountryCodes::getUnicodeSet();
        } else {
            list($start, $end) = Mappings::getUnicodeRange($type);
            $set = Mappings::generateUnicodeSet($start, $end);
        }

        $shebang = '';
        $base = count($set);

        do {
            $remainder = $seed % $base;
            $shebang = $set[$remainder] . $shebang;
            $seed = intdiv($seed, $base);
        } while ($seed > 0);

        return $shebang . self::SHEBANG_DELIMITER;
    }

    /**
     * @param string $root
     * @param string $prefix
     * @return bool
     * @throws \Exception
     */
    public function addAutoloader(string $root = __DIR__, string $prefix = ''): bool
    {
        $callback = function ($filePath) {
            $this->include($filePath);
        };
        return parent::registerAutoloader($callback, null, null, $root, $prefix);
    }

    /**
     * @param string $filePath
     * @return bool
     * @throws \Exception
     */
    public function include(string $filePath): bool
    {
        $contents = $this->readUTF8File($filePath);
        if (!$contents) {
            return false;
        }
        $pos = mb_strpos($contents, self::SHEBANG_DELIMITER);
        if ($pos === false) {
            return false;
        }
        $firstChar = mb_substr($contents, 0, 1);
        $type =  Mappings::sniffTypeSet($firstChar);
        if (!$type) {
            return false;
        }
        if (!Mappings::validateType($type, mb_substr($contents, $pos + 1))) {
            return false;
        }
        $contentsAfterShebang = mb_substr($contents, $pos + 1);
        if (!Mappings::validateType($type, $contentsAfterShebang)) {
            return false;
        }
        list ($type, $seed, $content) = self::readShebang($contents);
        $mapping = new Mappings($type, $seed);
        $decoded = $mapping->decode($content);
        eval($decoded);
        return true;
    }

    /**
     * @param string $contents
     * @return bool
     */
    public static function hasShebang(string $contents): bool
    {
        $pos = mb_strpos($contents, self::SHEBANG_DELIMITER);
        return $pos !== false;
    }
}
