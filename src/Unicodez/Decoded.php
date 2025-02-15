<?php

declare(strict_types=1);

namespace Unicodez;

/**
 * represents decoded unicodez content
 */
class Decoded
{
    /**
     * @var string[]|null
     */
    private array|null $lastDecodedLines = null;

    /**
     * @var \Throwable
     */
    private \Throwable $lastError;

    /**
     * @param string $decoded
     * @param string $type
     * @param int $seed
     */
    public function __construct(
        public string $decoded,
        public string $type,
        public int $seed
    ) {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->decoded;
    }

    /**
     * @return string|null
     */
    public function eval(): ?string
    {
        $decoded = $this->decoded;
        if (str_starts_with($decoded, "<?php")) {
            $decoded = substr($decoded, 6);
        }
        if (str_ends_with($decoded, "?>")) {
            $decoded = substr($decoded, 0, -3);
        }
        $decodedLines = preg_split("/\r\n|\n|\r/", $decoded);
        try {
            set_error_handler(function ($errno, $errstr) {
                throw new \ErrorException($errstr, $errno);
            });
            ob_start();
            eval($decoded);
            return ob_get_clean();
        } catch (\Throwable $e) {
            $this->lastError = $e;
            $this->lastDecodedLines = $decodedLines;
            return null;
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @return \Throwable
     */
    public function getLastError(): \Throwable
    {
        return $this->lastError;
    }

    /**
     * @return string[]|null
     */
    public function getLastDecodedLines(): array|null
    {
        return $this->lastDecodedLines;
    }
}
