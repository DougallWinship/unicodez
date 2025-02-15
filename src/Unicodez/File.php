<?php

declare(strict_types=1);

namespace Unicodez;

/**
 * represents file information
 */
class File
{
    public const string FILETYPE_UNENCODED_PHP = "PHP";

    public const string FILETYPE_ENCODED_SHEBANG = "Shebang";

    public const string FILETYPE_ENCODED_SEED = "Seed";

    public const string FILETYPE_OTHER = "Other";
    private string $filePath;
    private array $pathInfo;

    private string $fileType;
    private ?string $contents = null;
    private string $encodeType;
    private int $encodeSeed;

    /**
     * @param string $filePath
     * @throws \Exception
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Couldn't find file " . $filePath);
        } elseif (is_dir($filePath)) {
            throw new \Exception("Is a directory " . $filePath);
        }
        $this->filePath = $filePath;
        $this->pathInfo = pathinfo($filePath);
        if (!array_key_exists('extension', $this->pathInfo) || $this->pathInfo['extension'] !== "php") {
            $this->fileType = self::FILETYPE_OTHER;
            return;
        }
        $this->contents = file_get_contents($filePath);
        if (!$this->contents) {
            throw new \Exception("Failed to read contents of " . $filePath);
        }
        if (
            ShebangUnicodez::hasShebang($this->contents)
            &&
            ($result = ShebangUnicodez::readShebang($this->contents)
            )
        ) {
            $this->fileType = self::FILETYPE_ENCODED_SHEBANG;
            list($this->encodeType, $this->encodeSeed) = $result;
        } elseif ($type = Mappings::sniffTypeSet($this->contents)) {
            $this->fileType = self::FILETYPE_ENCODED_SEED;
            $this->encodeType = $type;
        } else {
            $this->fileType = self::FILETYPE_UNENCODED_PHP;
        }
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return bool
     */
    public function isEncoded(): bool
    {
        return $this->fileType === self::FILETYPE_ENCODED_SHEBANG || $this->fileType === self::FILETYPE_ENCODED_SEED;
    }

    /**
     * @return bool
     */
    public function isPHP(): bool
    {
        return $this->fileType === self::FILETYPE_UNENCODED_PHP;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getSummary(): string
    {
        return match ($this->fileType) {
            self::FILETYPE_OTHER =>
                "Other ." . $this->pathInfo['extension'] . " File",
            self::FILETYPE_UNENCODED_PHP =>
                "PHP File",
            self::FILETYPE_ENCODED_SHEBANG =>
                "Encoded Shebang File type=" . $this->encodeType . " seed=" . $this->encodeSeed,
            self::FILETYPE_ENCODED_SEED =>
                "Encoded Seed File type=" . $this->encodeType,
            default =>
                throw new \Exception("Unknown file type " . $this->fileType),
        };
    }

    /**
     * @return string
     */
    public function getEncodeType(): string
    {
        return $this->encodeType;
    }

    /**
     * @return int
     */
    public function getEncodeSeed(): int
    {
        return $this->encodeSeed;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        if (!$this->contents) {
            $this->contents = file_get_contents($this->filePath);
        }
        return $this->contents;
    }

    /**
     * @param $content
     * @return false|int
     */
    public function writeContent($content): false|int
    {
        $this->contents = $content;
        return file_put_contents($this->filePath, $content);
    }

    /**
     * @return string the backup file path
     */
    public function writeBackup(): string
    {
        $backupPath = $this->filePath . ".bak";
        file_put_contents($backupPath, $this->contents);
        return $backupPath;
    }
}
