# Unicodez
Encode/decode text to/from various Unicode character sets (ranges) using a seed for some reason 🤷

For example "this is a test" encoded as Runic with a seed of 1 gives:
ᚡ﻿ᛚᛁᛖᚰᛡᛑᚮᛅᛈᛉᛡᛑᚮᛅᛈᛉᛊᛔᛈᛉᛚᛁᛟᛩᚮᛅᛚᛁ

...or "Why am I looking at this project? I probably need to rethink my priorities." encoded with Flags and a seed of 13 results in:  
🇧🇩﻿🇹🇴🇧🇫🇱🇾🇲🇾🇰🇮🇰🇬🇸🇮🇸🇧🇬🇬🇵🇪🇩🇪🇩🇪🇸🇮🇸🇧🇲🇼🇾🇪🇸🇮🇸🇧🇦🇷🇬🇶🇬🇷🇧🇧🇬🇷🇧🇧🇸🇳🇮🇲🇦🇲🇨🇾🇧🇷🇯🇪🇹🇳🇵🇾🇸🇮🇸🇧🇬🇬🇵🇪🇩🇿🇲🇼🇸🇮🇸🇧🇩🇿🇲🇼🇱🇾🇲🇾🇦🇲🇨🇾🇨🇿🇸🇰🇸🇮🇸🇧🇻🇪🇸🇳🇫🇷🇮🇲🇬🇷🇧🇧🇮🇶🇬🇭🇧🇼🇲🇨🇬🇧🇲🇻🇩🇿🇲🇼🇸🇻🇩🇯🇸🇮🇸🇧🇲🇼🇾🇪🇸🇮🇸🇧🇻🇪🇸🇳🇫🇷🇮🇲🇬🇷🇧🇧🇪🇷🇬🇩🇬🇬🇵🇪🇪🇷🇬🇩🇦🇷🇬🇶🇰🇮🇰🇬🇸🇮🇸🇧🇧🇷🇯🇪🇧🇼🇲🇨🇧🇼🇲🇨🇰🇿🇧🇿🇸🇮🇸🇧🇩🇿🇲🇼🇬🇷🇧🇧🇸🇮🇸🇧🇫🇷🇮🇲🇧🇼🇲🇨🇩🇿🇲🇼🇱🇾🇲🇾🇦🇲🇨🇾🇧🇷🇯🇪🇸🇳🇮🇲🇸🇮🇸🇧🇩🇪🇩🇪🇰🇮🇰🇬🇸🇮🇸🇧🇻🇪🇸🇳🇫🇷🇮🇲🇦🇲🇨🇾🇬🇷🇧🇧🇫🇷🇮🇲🇦🇲🇨🇾🇩🇿🇲🇼🇦🇲🇨🇾🇧🇼🇲🇨🇨🇿🇸🇰🇸🇷🇵🇰

A pseudo-shebang indicates both the encoding range (set) and the seed used, a zero-width no-break space (U+FEFF) is used 
to delimit the pseudo-shebang.
> This pseudo-shebang "feature" ensures that using this library is essentially useless for obfuscation (since
> the seed is automatically decoded), this perfectly aligns with the fundamental pointlessness of the project. 

> Note that currently only 8-bit encoding is supported, which is sufficient to cover ASCII, but not much more!

## Getting started
The project uses composer to generate a PSR-4 autoloader, so to get started
```
composer install
```

To play with the dumbness, either mount /public on a local webserver, or use the PHP webserver:
```
cd public
php -S localhost:8000
```

and look at:
```
Encoder/Decoder (/encoder-decoder.php) : translate to/from the unicodez text
Encode/Decode Test (/encode-decode-test.php) : basic HTML test of each set
Autoload Test (/autoload-test.php) : show unicodez auto-loading in practice!
```

## PHP

### Basic encoding/decoding
```php
$unicodez = new \Unicodez\Unicodez();
$encoded = $unicodez->encode("This is some text", \Unicodez\Mappings::TEXT_RUNIC, 123);
$decoded = $unicodez->decode($encoded)
```

### Include/Autoloader
While basic text encoding/decoding is fine on its own for any arbitrary text, since this is implemented in PHP an
include method and autoloader are also supplied for your convenience:
```php
$unicodez = new \Unicodez\Unicodez();
$unicodez->include(dirname(__DIR__) . '/src/Runic/RunicTest.php')
```
```php
$unicodez = new \Unicodez\Unicodez();
$unicodez->addAutoloader(dirname(__DIR__) . '/src');
$runicTest = new \Runic\RunicTest();
```
This finds the file, then decodes and ~~evils~~/evals it.

> This attempts to add a reasonably sensible autoloader at the start of the PHP autoloader chain 
> (using the prepend attribute), the hope is that you may be able to annoy/confound your colleagues/enemies by 
> providing php implementations like [the Runic autoload test script](./src/Runic/RunicTest.php).

## PSR-12
The code conforms to the [PSR-12](https://www.php-fig.org/psr/psr-12/) standard.
This is the tool that is used to check : https://github.com/PHPCSStandards/PHP_CodeSniffer/
Once installed and available globally (presumably via PATH settings), something like this can be used from the root dir:
```
phpcs --standard=PSR12 ./src/Unicodez/
```