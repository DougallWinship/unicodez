<?php

declare(strict_types=1);

namespace Unicoder;

class Cache {

    private string $cacheDir;

    public function __construct() {
        $this->cacheDir = $this->getUnicoderDirectory();
    }

    function getUnicoderDirectory(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $baseDir = getenv('LOCALAPPDATA') . DIRECTORY_SEPARATOR . 'Unicoder';
        } else {
            $baseDir = getenv('HOME') . DIRECTORY_SEPARATOR . '.unicoder';
        }

        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        return $baseDir;
    }

    function has($type, $seed): bool
    {
        return file_exists($this->getFile($type, $seed));
    }

    function store($type, $seed, $mapping): false|int
    {
        $filePath = $this->getFile($type, $seed);
        return file_put_contents($filePath, serialize($mapping));
    }

    function load($type, $seed): ?array
    {
        $filePath = $this->getFile($type, $seed);
        return file_exists($filePath)
            ? unserialize(file_get_contents($filePath))
            : null;
    }

    public function clear($type = null, $seed = null): void
    {
        if ($type && $seed) {
            // Clear a specific file based on type and seed
            $filePath = $this->getFile($type, $seed);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } elseif ($type) {
            // Clear all files of a specific type
            $files = glob($this->cacheDir . DIRECTORY_SEPARATOR . $type . "-*.php");
            foreach ($files as $file) {
                unlink($file);
            }
        } else {
            // Clear all cache files
            $files = glob($this->cacheDir . DIRECTORY_SEPARATOR . '*.php');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    function getFile($type, $seed): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . str_replace(" ","-",strtolower($type)) . "-" . $seed . ".php";
    }
}