<?php

declare(strict_types=1);

namespace Unicoder;

class Mappings {

    const int DEFAULT_ENCODE_BITS = 16;

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

    private string $type;
    private int $seed;

    private int $bits;

    private int $comboLength;

    private Cache $cache;

    private array $mappings;

    private array $flippedMappings;

    public function __construct(string $type, int $seed) {
        if (!in_array($type, self::ALL_TYPES)) {
            throw new \Exception("Unsupported type : ".$type);
        }
        $this->type = $type;
        $this->seed = $seed;
        $this->bits = self::DEFAULT_ENCODE_BITS;
        $this->cache = new Cache();

        if (!$this->cache->has($type, $seed)) {
            if ($type === self::TYPE_FLAGS) {
                $unicodeSet = CountryCodes::getUnicodeSet();
            }
            else {
                $range = self::getUnicodeRange($type);
                $unicodeSet = self::generateUnicodeSet($range[0], $range[1]);
            }
            $this->comboLength = self::determineComboLength(count($unicodeSet), $this->bits);
            // Ensure we have enough combinations to cover the encoding space
            if (pow(count($unicodeSet), $this->comboLength) < pow(2, $this->bits)) {
                throw new \Exception("Not enough characters in the set to cover the encoding space.");
            }
            $this->mappings = $this->generateMappings($unicodeSet);
            $this->cache->store($type, $seed, $this->mappings);
        }
        else {
            $this->mappings = $this->cache->load($type, $seed);
        }
        $this->flippedMappings =array_flip($this->mappings);
    }

    public function getType(): string { return $this->type; }
    public function getSeed(): int { return $this->seed; }
    public function getBits(): int { return $this->bits; }

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
     * Decoding logic
     * @param string $encodedStr
     * @return string
     */
    public function decode(string $encodedStr): string {
        $decoded = '';
        $chunks = preg_split('//u', $encodedStr, -1, PREG_SPLIT_NO_EMPTY);
        $buffer = '';

        $flippedMappings = $this->flippedMappings;
        foreach ($chunks as $char) {
            $buffer .= $char;
            if (isset($flippedMappings[$buffer])) {
                $binary = $flippedMappings[$buffer];
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

    private function generateMappings($unicodeSet): array
    {
        srand($this->seed);  // Initialize random generator with the seed

        // Shuffle the list of Unicode characters
        shuffle($unicodeSet);

        $combinations = [];
        $this->generateCombinationsRecursive($unicodeSet, '', $this->comboLength, $combinations);

        shuffle($combinations);

        // Create mappings for the required binary values
        $mappings = [];
        for ($i = 0; $i < pow(2, $this->bits); $i++) {
            $mappings[sprintf('%0' . $this->bits . 'b', $i)] = $combinations[$i];
        }
        return $mappings;
    }

    private function generateCombinationsRecursive(array $set, string $prefix, int $comboLength, array &$combinations): void {
        if ($comboLength == 0) {
            $combinations[] = $prefix;
            return;
        }

        foreach ($set as $char) {
            $this->generateCombinationsRecursive($set, $prefix . $char, $comboLength - 1, $combinations);
        }
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

    public static function sniffTypeSet(string $shebang): string
    {
        $countryCodes = CountryCodes::getUnicodeSet();
        if (in_array(mb_substr($shebang,0,2), $countryCodes)) {
            return self::TYPE_FLAGS;
        }
        $charCode = mb_ord(mb_substr($shebang,0,1), 'UTF-8');
        for ($i=0; $i<count(self::ALL_TYPES); $i++) {
            $checkType = self::ALL_TYPES[$i];
            list($start, $end) = self::getUnicodeRange($checkType);
            if ($charCode >= $start && $charCode <= $end) {
                return $checkType;
            }
        }
        throw new \Exception("Failed to determine type!");
    }

    public static function generateUnicodeSet(int $from, int $to): array
    {
        $set = [];
        for ($codePoint = $from; $codePoint <= $to; $codePoint++) {
            $set[] = mb_chr($codePoint);
        }
        return $set;
    }

    public static function determineComboLength(int $setSize, int $numBits): int
    {
        // e.g., 65536 for 16-bit encoding
        $requiredCombinations = pow(2, $numBits);
        $comboLength = 1;

        while (pow($setSize, $comboLength) < $requiredCombinations) {
            $comboLength++;
        }

        return $comboLength;
    }
}