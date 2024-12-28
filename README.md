# Unicoder
Encode/decode text to/from various Unicode character ranges using a seed ... for shits & giggles!
Yup this is a PHP project, live with it.
Additionally, a pseudo-shebang indicates both the encoding range (set) and the seed used.

## Getting started
The project uses composer to generate a PSR-4 autoloader, so to get started
```
composer install
```

To play with the dumbness either mounting /public on a local webserver or use the PHP webserver:
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
```
$unicoder = new Unicoder();
$encoded = $unicoder->encode("This is some text", Mappings::TEXT_RUNIC, 123);
$decoded = $unicoder->decode($encoded)
```
## Include/Autoloader
While basic text encoding/decoding is fine on its own for any arbitrary text, since this is implemented in PHP an
autoloader is also supplied for your convenience:
