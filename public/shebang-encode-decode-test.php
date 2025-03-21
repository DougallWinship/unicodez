<?php
require_once("layout-prepare.php");

$title = "Unicodez : Shebang Encode/Decode Test";

$seed = intval($_GET["seed"] ?? 1);
?>
<form action="" method="GET">
    <label>Seed : <input name="seed" type="number" value="<?= $seed ?>" min="1" step="1" /></label>
</form>
<br><br><hr><br>
<?php

foreach (\Unicodez\Mappings::ALL_TYPES as $type) {
    $unicoder = new \Unicodez\ShebangUnicodez();
    echo "Type : ".$type."<br>".PHP_EOL;
    $php = <<<PHP
echo "Hello from $type!";
PHP;
    echo "PHP : ".htmlspecialchars($php)."<br>".PHP_EOL;
    $encoded = $unicoder->encode($php, $type, $seed);
    //list($type, $seed, $decoded)
    $decoded = $unicoder->decode($encoded);
    $type = $decoded->type;
    $seed = $decoded->seed;
    echo "Encoded : ".$encoded."<br>".PHP_EOL;
    echo "Decoded : ".htmlspecialchars($decoded)."<br>".PHP_EOL;

    echo "Eval:<br>".PHP_EOL;
    echo "<div style='padding:4px;border:1px solid #ccc;display:inline-block'>";
    eval($decoded);
    echo "</div>";

    echo "<br><br><hr><br>".PHP_EOL;
}
require_once("layout-render.php");