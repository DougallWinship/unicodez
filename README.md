# Unicoder
Encode/decode text (8-bit max currently) to/from various Unicode character ranges using a seed for shits & giggles!

For example "hello!" encoded as unicode flags with a seed of 1 generates
🇪🇸🇳🇿🇸🇳🇰🇭🇹🇿🇲🇳🇹🇿🇲🇳🇩🇪🇭🇺🇻🇪🇸🇴

The project uses composer to generate a PSR-4 autoloader, so to get started
1. composer install
2. mount /public on your local dev webserver

## Sets
- the sets are defined as constants in the [Unicoder class](./src/Unicoder/Unicoder.php)

## Auto-loading
- encoded php files currently use the .uphp extension
- there is a basic php autoloader which decodes/loads encoded .uphp classes on the fly
  see [autoload-test.php](./public/autoload-test.php)
- alternatively there is a direct include method 
