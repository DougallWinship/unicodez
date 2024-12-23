<?php

declare(strict_types=1);

namespace Unicoder;

class Unicoder {

    const int DEFAULT_ENCODE_BITS = 8;

    const int DEFAULT_SEED = 1;

    const string SHEBANG_DELIMITER = "\u{200B}";

    const string AUTOLOADER_EXTENSION = "uphp";

    const string TYPE_FLAGS = "Flags";
    const string TYPE_OGHAM = "Ogham";
    const string TYPE_RUNIC = "Runic";
    const string TYPE_BRAILLE = "Braille";
    const string TYPE_ARROWS = "Arrows";
    const string TYPE_MATHS_OPERATORS = "Maths Operators";
    const string TYPE_BOX_DRAWING = "Box Drawing";
    const string TYPE_GEOMETRIC_SHAPES = "Geometric Shapes";
    const string TYPE_PLANETS = "Planets";
    const string TYPE_ZODIAC = "Zodiac";
    const string TYPE_CHESS = "Chess Pieces";
    const string TYPE_CARD_SUITES = "Card Suites";
    const string TYPE_MUSIC_NOTES = "Music Notes";
    const string TYPE_DICE = "Dice";
    const string TYPE_CUNIEFORM = "Cunieform";
    const string TYPE_HEIROGLYPHS = "Heiroglyphs";
    const string TYPE_EMOTICONS = "Emoticons";
    const string TYPE_TRANSPORT = "Transport";
    const string TYPE_ALCHEMICAL = "Alchemical";

    const array ALL_TYPES =[
        self::TYPE_FLAGS,
        self::TYPE_OGHAM,
        self::TYPE_RUNIC,
        self::TYPE_BRAILLE,
        self::TYPE_ARROWS,
        self::TYPE_MATHS_OPERATORS,
        self::TYPE_BOX_DRAWING,
        self::TYPE_GEOMETRIC_SHAPES,
        self::TYPE_PLANETS,
        self::TYPE_ZODIAC,
        self::TYPE_CHESS,
        self::TYPE_CARD_SUITES,
        self::TYPE_MUSIC_NOTES,
        self::TYPE_DICE,
        self::TYPE_CUNIEFORM,
        self::TYPE_HEIROGLYPHS,
        self::TYPE_EMOTICONS,
        self::TYPE_TRANSPORT,
        self::TYPE_ALCHEMICAL,
    ];

    private array $unicodeSet;
    private string $type;
    private int $seed;
    private int $bits;
    private int $comboLength;
    private array $mappings;
    private array $flippedMappings;
    private string $content;

    /**
     * @param string $encoded
     * @return self
     * @throws \Exception
     */
    public static function ShebangUnicoder(string $encoded): self
    {
        list ($seed, $type, $content) = self::readShebang($encoded);
        $unicoder = new Unicoder($type, $seed);
        $unicoder->content = $content;
        return $unicoder;
    }

    /**
     * @param string $type
     * @param int $seed
     * @param int $bits
     * @throws \Exception
     */
    public function __construct(string $type, int $seed=self::DEFAULT_SEED, int $bits=self::DEFAULT_ENCODE_BITS) {

        if ($type === self::TYPE_FLAGS) {
            $this->unicodeSet = CountryCodes::getUnicodeSet();
        }
        else {
            $startEnd = $this->getUnicodeRange($type);
            if (!$startEnd) {
                throw new \Exception("Unrecognised type $type");
            }
            $this->unicodeSet = $this->generateUnicodeSet($startEnd[0], $startEnd[1]);
        }
        if (!$this->unicodeSet) {

        }
        $this->type = $type;
        $this->seed = $seed;
        $this->bits = $bits;
        $this->comboLength = $this->determineComboLength(count($this->unicodeSet), $this->bits);

        // Ensure we have enough combinations to cover the encoding space
        if (pow(count($this->unicodeSet), $this->comboLength) < pow(2, $this->bits)) {
            throw new \Exception("Not enough characters in the set to cover the encoding space.");
        }

        $this->generateBindings();
    }

    public static function getUnicodeRange($type): ?array {
        return match($type) {
            self::TYPE_OGHAM => [0x1680, 0x169A],
            self::TYPE_RUNIC => [0x16A0, 0x16EA],
            self::TYPE_BRAILLE => [0x2800, 0x28FF],
            self::TYPE_ARROWS => [0x2190, 0x21FF],
            self::TYPE_MATHS_OPERATORS => [0x2200, 0x22FF],
            self::TYPE_BOX_DRAWING => [0x2500, 0x257F],
            self::TYPE_GEOMETRIC_SHAPES => [0x25A0, 0x25FF],
            self::TYPE_PLANETS => [0x263C, 0x2647],
            self::TYPE_ZODIAC => [0x2648, 0x2653],
            self::TYPE_CHESS => [0x2654, 0x265F],
            self::TYPE_CARD_SUITES => [0x2660, 0x2667],
            self::TYPE_MUSIC_NOTES => [0x2669, 0x266E],
            self::TYPE_DICE => [0x2680, 0x2685],
            self::TYPE_CUNIEFORM => [0x12000, 0x12399],
            self::TYPE_HEIROGLYPHS => [0x13000, 0x1342E],
            self::TYPE_EMOTICONS => [0x1F600, 0x1F64F],
            self::TYPE_TRANSPORT => [0x1F680, 0x1F6C5],
            self::TYPE_ALCHEMICAL => [0x1F700, 0x1F773],
            default => null
        };
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSeed(): int
    {
        return $this->seed;
    }

    public function getUnicodeSet(): array
    {
        return $this->unicodeSet;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param int $from
     * @param int $to
     * @return array
     */
    private static function generateUnicodeSet(int $from, int $to): array
    {
        $set = [];
        for ($codePoint = $from; $codePoint <= $to; $codePoint++) {
            $set[] = mb_chr($codePoint);
        }
        return $set;
    }

    /**
     * @param int $setSize
     * @param int $numBits
     * @return int
     */
    private function determineComboLength(int $setSize, int $numBits): int
    {
        $requiredCombinations = pow(2, $numBits);  // e.g., 65536 for 16-bit encoding
        $comboLength = 1;

        while (pow($setSize, $comboLength) < $requiredCombinations) {
            $comboLength++;
        }

        return $comboLength;
    }

    /**
     * @return void
     */
    private function generateBindings(): void
    {
        srand($this->seed);  // Initialize random generator with the seed

        // Shuffle the list of Unicode characters
        $shuffledSet = $this->unicodeSet;
        shuffle($shuffledSet);

        $combinations = [];
        $this->generateCombinationsRecursive($shuffledSet, '', $this->comboLength, $combinations);

        shuffle($combinations);

        // Create mappings for the required binary values
        for ($i = 0; $i < pow(2, $this->bits); $i++) {
            $this->mappings[sprintf('%0' . $this->bits . 'b', $i)] = $combinations[$i];
        }

        // Generate the flipped mappings for decoding
        $this->flippedMappings = array_flip($this->mappings);
    }

    /**
     * Recursively generate combinations based on the set size and combo length
     * @param array $set
     * @param string $prefix
     * @param int $comboLength
     * @param array $combinations
     * @return void
     */
    private function generateCombinationsRecursive(array $set, string $prefix, int $comboLength, array &$combinations): void {
        if ($comboLength == 0) {
            $combinations[] = $prefix;
            return;
        }

        foreach ($set as $char) {
            $this->generateCombinationsRecursive($set, $prefix . $char, $comboLength - 1, $combinations);
        }
    }

    /**
     * Encoding logic
     * @param $input
     * @return string
     */
    public function encode($input): string
    {
        $encoded = '';
        foreach (mb_str_split($input) as $char) {
            $binary = sprintf('%0' . $this->bits . 'b', mb_ord($char));  // Handle 16-bit binary values
            $encoded .= $this->mappings[$binary];
        }
        return $encoded;
    }

    /**
     * @param string $input
     * @return array
     * @throws \Exception
     */
    public static function readShebang(string $input): array
    {
        // Find the position of the delimiter
        $position = mb_strpos($input, self::SHEBANG_DELIMITER, 0, 'UTF-8');

        if ($position === false) {
            throw new \Exception('Shebang Delimiter not found in input.');
        }

        $shebang = mb_substr($input, 0, $position, 'UTF-8');
        $firstCharacter = mb_substr($input, 0, 1, 'UTF-8');
        $content = mb_substr($input, $position+1, null, 'UTF-8');
        list($type, $set) = self::sniffTypeSet($firstCharacter);
        $base = count($set);
        $mapCharsToIndices = array_flip($set);
        $seed = 0;
        $length = mb_strlen($shebang,'UTF-8');
        for ($idx=0; $idx<$length; $idx++) {
            $char = mb_substr($shebang, $idx, 1, 'UTF-8');
            if (!isset($mapCharsToIndices[$char])) {
                throw new \Exception("Invalid character in shebang '{$char}'");
            }
            $seed = $seed * $base + $mapCharsToIndices[$char];
        }
        return [$seed, $type, $content];
    }

    /**
     * @param string $char
     * @return array
     * @throws \Exception
     */
    private static function sniffTypeSet(string $char): array
    {
        if (mb_strlen($char)!==1) {
            throw new \Exception('A single character should be provided to sniffType');
        }
        $charCode = mb_ord($char, 'UTF-8');
        for ($i=0; $i<count(self::ALL_TYPES); $i++) {
            $checkType = self::ALL_TYPES[$i];
            list($start, $end) = self::getUnicodeRange($checkType);
            if ($charCode >= $start && $charCode <= $end) {
                return [$checkType, self::generateUnicodeSet($start, $end)];
            }
        }
        $countryCodes = CountryCodes::getUnicodeSet();
        if (in_array($charCode, $countryCodes)) {
            return [self::TYPE_FLAGS, $countryCodes];
        }
        throw new \Exception("Failed to determine type!");
    }

    public function generateShebang(): string
    {
        list($start, $end) = self::getUnicodeRange($this->type);

        $base = $end - $start;
        $shebang = '';

        $set = self::generateUnicodeSet($start, $end);

        do {
            $remainder = $this->seed % $base;
            $shebang = $set[$remainder] . $shebang;
            $seed = intdiv($this->seed, $base);
        } while ($seed > 0);

//        $minLength = ceil(log(10000, $base)); // 4 digits in base-N
//        while (mb_strlen($shebang, 'UTF-8') < $minLength) {
//            $shebang = $set[0] . $shebang; // Pad with the first character in the set
//        }

        return $shebang.self::SHEBANG_DELIMITER;
    }

    /**
     * Decoding logic
     * @param string $encodedStr
     * @return string
     */
    public function decode(string $encodedStr): string {
        $decoded = '';
        $chunks = preg_split('//u', $encodedStr, -1, PREG_SPLIT_NO_EMPTY);
        $buffer = '';

        foreach ($chunks as $char) {
            $buffer .= $char;
            if (isset($this->flippedMappings[$buffer])) {
                $binary = $this->flippedMappings[$buffer];
                $decoded .= mb_chr(bindec($binary));  // Convert back to Unicode character
                $buffer = '';  // Reset buffer for next combination
            }
        }

        if (str_starts_with($decoded, "<?php")) {
            $decoded = substr($decoded, 6);
        }
        if (str_ends_with($decoded, "?>")) {
            $decoded = substr($decoded, 0, -3);
        }

        return $decoded;
    }

    /**
     * @param string $root
     * @param string $prefix
     * @return void
     * @throws \Exception
     */
    public function addAutoloader(string $root=__DIR__, string $prefix = ''): void
    {
        spl_autoload_register(function ($className) use ($root, $prefix) {
            if ($prefix && str_starts_with($className, $prefix)) {
                return;
            }
            $relativeClass = $prefix ? substr($className, strlen($prefix)) : $className;
            $relativeClass = ltrim($relativeClass, '\\');
            $filePath = $root.'/'.str_replace('\\', '/', $relativeClass).'.'.self::AUTOLOADER_EXTENSION;
            $this->include($filePath);
        });
    }

    /**
     * @param string $filePath
     * @return void
     * @throws \Exception
     */
    public function include(string $filePath): void
    {
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);
            $detected =  mb_detect_encoding($contents,"UTF-8, ISO-8859-1", true);
            if ($detected != "UTF-8") {
                $contents = mb_convert_encoding($contents, "UTF-8", $detected);
            }
            $decoded = $this->decode($contents);
            eval($decoded);
        }
        else {
            throw new \Exception("File not found: $filePath");
        }
    }
}