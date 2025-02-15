# Unicodez
Encode/decode text to/from various Unicode character sets (ranges) using a seed for some reason 🤷

For example "this is a test" encoded as Runic with a seed of 1 (using a pseudo shebang to integrate the seed number) gives:
ᚡ﻿ᛚᛁᛖᚰᛡᛑᚮᛅᛈᛉᛡᛑᚮᛅᛈᛉᛊᛔᛈᛉᛚᛁᛟᛩᚮᛅᛚᛁ

...or "Why am I looking at this project? I probably need to rethink my priorities." encoded with Flags and a seed of 13 (without a pseudo-shebang) results in:  
🇹🇴🇧🇫🇱🇾🇲🇾🇰🇮🇰🇬🇸🇮🇸🇧🇬🇬🇵🇪🇩🇪🇩🇪🇸🇮🇸🇧🇲🇼🇾🇪🇸🇮🇸🇧🇦🇷🇬🇶🇬🇷🇧🇧🇬🇷🇧🇧🇸🇳🇮🇲🇦🇲🇨🇾🇧🇷🇯🇪🇹🇳🇵🇾🇸🇮🇸🇧🇬🇬🇵🇪🇩🇿🇲🇼🇸🇮🇸🇧🇩🇿🇲🇼🇱🇾🇲🇾🇦🇲🇨🇾🇨🇿🇸🇰🇸🇮🇸🇧🇻🇪🇸🇳🇫🇷🇮🇲🇬🇷🇧🇧🇮🇶🇬🇭🇧🇼🇲🇨🇬🇧🇲🇻🇩🇿🇲🇼🇸🇻🇩🇯🇸🇮🇸🇧🇲🇼🇾🇪🇸🇮🇸🇧🇻🇪🇸🇳🇫🇷🇮🇲🇬🇷🇧🇧🇪🇷🇬🇩🇬🇬🇵🇪🇪🇷🇬🇩🇦🇷🇬🇶🇰🇮🇰🇬🇸🇮🇸🇧🇧🇷🇯🇪🇧🇼🇲🇨🇧🇼🇲🇨🇰🇿🇧🇿🇸🇮🇸🇧🇩🇿🇲🇼🇬🇷🇧🇧🇸🇮🇸🇧🇫🇷🇮🇲🇧🇼🇲🇨🇩🇿🇲🇼🇱🇾🇲🇾🇦🇲🇨🇾🇧🇷🇯🇪🇸🇳🇮🇲🇸🇮🇸🇧🇩🇪🇩🇪🇰🇮🇰🇬🇸🇮🇸🇧🇻🇪🇸🇳🇫🇷🇮🇲🇦🇲🇨🇾🇬🇷🇧🇧🇫🇷🇮🇲🇦🇲🇨🇾🇩🇿🇲🇼🇦🇲🇨🇾🇧🇼🇲🇨🇨🇿🇸🇰🇸🇷🇵🇰

> Note that currently only 8-bit encoding is supported, which is sufficient to cover ASCII, but not much more!

## Getting started:
The project uses composer to generate a PSR-4 autoloader, so to get started simply
```
composer install
```
There a single dependency on [php-cli-tools](https://github.com/wp-cli/php-cli-tools).

## Usage:
As indicated there are two types of encoding:
### 1. Using a pseudo-shebang
- this integrates the seed into the encoded text, meaning it can be decoded without knowing the seed.
- the pseudo-shebang indicates both the encoding range (set) and the seed used, a zero-width no-break space (U+FEFF) is 
  used to delimit the pseudo-shebang.
- hence this technique cannot be used for obfuscation, and is essentially just for amusement!
- example:
```php
$unicodez = new \Unicodez\ShebangUnicodez();
$encoded = $unicodez->encode("This is some text", \Unicodez\Mappings::TEXT_RUNIC, 123);
$decoded = $unicodez->decode($encoded)
```

### 2. Using an explicit seed
- this means the seed must be known to decode the encoded text.
- hence this technique can be used for basic obfuscation.
- example:
```php
$unicodez = new \Unicodez\SeedUnicodez();
$encoded = $unicodez->encode("This is some text", \Unicodez\Mappings::TEXT_RUNIC, 123);
$decoded = $unicodez->decode($encoded, 123)
```

## Tools/Examples
You can mount /public on a local webserver, or use the PHP webserver:
```
cd public
php -S localhost:8000
```
and look at:
- [Shebang Encoder/Decoder](./public/shebang-encoder-decoder.php) translate to/from the shebang unicodez text
- [Shebang Encode/Decode Test](./public/shebang-encode-decode-test.php) basic HTML test of each set using a shebang
- [Shebang Autoload Test](./public/shebang-autoload-test.php) show unicodez shebang autoloading in practice
- [Seed Encoder/Decoder](./public/seed-encoder-decoder.php) translate to/from the seeded unicodez text
- [Seed Encode/Decode Test](./public/shebang-encode-decode-test.php) basic HTML test of each set using a seed
- [Seed Autoload Test](./public/shebang-autoload-test.php) show unicodez seed autoloading in practice


## Include/Autoloader
Since this is implemented in PHP an include method and autoloader are also provided:

These both attempt to decode and ~~evils~~/evals the content.

[Here](./public/shebang-autoload-test.php) is an example of using the shebang autoloader.

> This attempts to add a reasonably sensible autoloader at the start of the PHP autoloader chain
> (using the prepend attribute), the hope is that you may be able to annoy/confound your colleagues/enemies by
> providing php implementations like [the Runic autoload test script](./src/Runic/ShebangRunicTest.php).

Here is how to use the shebang include method:
```php
$unicodez = new \Unicodez\ShebangUnicodez();
$unicodez->include("/src/Runic/SeedRunicTest");
```

[Here](./public/seed-autoload-test.php) is an example using the seeded autoloader.

Here is how to use the seed include method:
```php
$unicodez = new \Unicodez\SeedUnicodez();
$unicodez->include("/src/Runic/SeedRunicTest", 1);
```
> Note that in this case you *must* provide a seed for decoding, the mapping is determined automatically,
> but the seed can be kept secret in order to provide trivial obfuscation.

## Command Line Tool
There is a command line tool which can be used to bulk encode/decode files.

Example of shebang encoding all files in a directory into Runic with prompts and backups:
```shell
./unicodez encode --target ~/Temp --flavour shebang --map Runic --seed 123 --write --verbose
```

Example of silently decoding all encoded files in a directory:
```shell
./uncodez decode --target ~/Temp --silent
```

The following commands are available:
- help
- version
- status
- encode
- decode
- cache-clear

## PSR-12
The code largely conforms to the [PSR-12](https://www.php-fig.org/psr/psr-12/) standard.
This is the tool that is used to check : https://github.com/PHPCSStandards/PHP_CodeSniffer/
Once installed and available globally (presumably via PATH settings), something like this can be used from the root dir:
```
phpcs --standard=PSR12 ./src/Unicodez/
```

