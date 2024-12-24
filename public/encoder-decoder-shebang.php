<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__).'/vendor/autoload.php';

$types = \Unicoder\Unicoder::ALL_TYPES;

$seed = intval($_POST['seed'] ?? \Unicoder\Unicoder::DEFAULT_SEED);

$unencoded=$_POST['unencoded'] ?? '';
$encoded=$_POST['encoded'] ?? '';
$output='';
$evalError = null;
$type = $_POST['type'] ?? null;
if ($type) {
    if (!in_array($type, $types)) {
        die("Unrecognized unicoder type : ".$type);
    }
    if (isset($_POST['do-encode'])) {
        $unicoder = new \Unicoder\Unicoder($type, $seed);
        $shebang = $unicoder->generateShebang();
        $encoded = $shebang.$unicoder->encode($unencoded);
    }
    else if (isset($_POST['do-decode'])) {
        $unicoder = \Unicoder\Unicoder::ShebangUnicoder($encoded);
        $encodedWithoutShebang = $unicoder->getContent();
        $seed = $unicoder->getSeed();
        $type = $unicoder->getType();
        $unencoded = $unicoder->decode($encodedWithoutShebang);
    }
    else if (isset($_POST['do-eval'])) {
        $unicoder = \Unicoder\Unicoder::ShebangUnicoder($encoded);
        $encodedWithoutShebang = $unicoder->getContent();
        $seed = $unicoder->getSeed();
        $type = $unicoder->getType();
        $evalUnencoded = $unicoder->decode($encodedWithoutShebang);
        if (str_starts_with($evalUnencoded,"<?php")) {
            $evalUnencoded = substr($evalUnencoded,6);
        }
        if (str_ends_with($evalUnencoded,"?>")) {
            $evalUnencoded = substr($evalUnencoded,0,-3);
        }
        $evalUnencodedLines = preg_split("/\r\n|\n|\r/", $evalUnencoded);
        try {
            ob_start();
            eval($evalUnencoded);
            $output = ob_get_clean();
        }
        catch (\ParseError $e) {
            $evalError = $e;
        }
        finally {
            ob_end_clean();
        }

    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Unicoder : Shebang Encode/Decode</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<h1 style="margin-bottom:0">Unicoder : Encode/Decode</h1>
<?php if (isset($unicoder)) {?>
<div style="position:relative;top:-10px;">Num bits = <?= $unicoder->getBits(); ?></div>
<?php } ?>
<form action="" method="post">
    <label>
        <textarea name="unencoded" rows="12" cols="80"><?= $unencoded ?></textarea>
        <br>
    </label>
    <div style="display: flex; flex-direction:  row"></div>
    <label>
        Seed : <input name="seed" type="number" value="<?= $seed ?>" min="1" step="1" style="width:100px" />
    </label>
    <label>
        Obfuscator :
        <select name="type">
            <?php foreach ($types as $optionType) {?>
                <option value="<?= $optionType ?>" <?= $optionType===$type ? 'selected' : '' ?>><?= $optionType ?></option>
            <?php } ?>
        </select>
    </label>
    <button  name="do-encode" value="1">Encode</button>

    <br><br><br>
    <label>
        <textarea name="encoded" rows="12" cols="80"><?= $encoded ?></textarea>
        <br>
    </label>
    <button name="do-decode" value="1">Decode</button>
    <button name="do-eval" value="1">PHP Eval</button>

    <?php if (isset($evalError) && isset($evalUnencodedLines)) {?>
        <?php for($line=1; $line<=count($evalUnencodedLines); $line++) { ?>
            <pre style="margin:0"><?= $line ?> : <?= $evalUnencodedLines[$line-1]; ?> <?php if ($evalError->getLine()===$line){?><span style="color:red"> &lt;= <?= $evalError->getMessage()?><?php } ?></pre>
        <?php } ?>
    <?php } else if ($output) { ?>
        <br>
        <div style="padding:6px;border:1px solid #aaa;display:inline-block;margin-top:6px"><?= $output ?></div>
    <?php } ?>
</form>
</body>
</html>