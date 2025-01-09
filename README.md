# Unicodez
Encode/decode text to/from various Unicode character sets (ranges) using a seed ... purely for shits & giggles!

For example "this is a test" encoded as Runic with a seed of 1 gives:
ᚡ﻿ᛚᛁᛖᚰᛡᛑᚮᛅᛈᛉᛡᛑᚮᛅᛈᛉᛊᛔᛈᛉᛚᛁᛟᛩᚮᛅᛚᛁ

A pseudo-shebang indicates both the encoding range (set) and the seed used, a zero-width no-break space (U+FEFF) is used 
to delimit the shebang.

> Note that currently only 8-bit encoding is supported, which is sufficient to cover ASCII, but not much more!

## Getting started
The project uses composer to generate a PSR-4 autoloader, so to get started
```
composer install
```

To play with the dumbness either mount /public on a local webserver or use the PHP webserver:
```
cd public
php -S localhost:8000
```

and look at:
```
Encoder/Decoder (/unicoder-decoder.php) : how to translate to/from the unicoder text
Encode/Decode Test (/encode-decode-test.php) : basic html test of each set
Autoload Test (/autoload-test.php) : show unicoder auto-loading in practice!
```

## PHP

### Basic encoding/decoding
```php
$unicoder = new Unicoder();
$encoded = $unicoder->encode("This is some text", Mappings::TEXT_RUNIC, 123);
$decoded = $unicoder->decode($encoded)
```

### Include/Autoloader
While basic text encoding/decoding is fine on its own for any arbitrary text, since this is implemented in PHP an
include method and autoloader are also supplied for your convenience:
```php
$unicoder = new \Unicoder\Unicoder();
$unicode->include(dirname(__DIR__) . '/src/Runic/RunicTest.php')
```
```php
$unicoder = new \Unicoder\Unicoder();
$unicoder->addAutoloader(dirname(__DIR__) . '/src');
$runicTest = new \Runic\RunicTest();
```
This finds the file, then decodes and evals it.
