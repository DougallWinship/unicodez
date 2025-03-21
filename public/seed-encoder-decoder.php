<?php
require_once("layout-prepare.php");

$title = "Unicodez : Seed Encoder/Decoder";

$types = \Unicodez\Mappings::ALL_TYPES;

$seed = intval($_POST['seed'] ?? 1);

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
        $unicoder = new \Unicodez\SeedUnicodez();
        $encoded = $unicoder->encode($unencoded, $type, $seed);
        $unencoded = '';
    }
    else if (isset($_POST['do-decode'])) {
        $unicoder = new \Unicodez\SeedUnicodez();
        $unencoded = $unicoder->decode($encoded, $seed);
        $encoded = '';
    }
    else if (isset($_POST['do-eval'])) {
        $unicoder = new \Unicodez\SeedUnicodez();
        $decoded = $unicoder->decode($encoded, $seed);
        $evalOutput = $decoded->eval();
    }
}
?>
<form action="" method="post">
    <div>
        <label style="margin-right:6px">
            Seed : <input name="seed" type="number" value="<?= $seed ?>" min="1" step="1" style="width:100px" />
        </label>
        <label style="margin-right:6px">
            Unicodez set :
            <select name="type">
                <?php foreach ($types as $optionType) {?>
                    <option value="<?= $optionType ?>" <?= $optionType===$type ? 'selected' : '' ?>><?= $optionType ?></option>
                <?php } ?>
            </select>
        </label>
    </div>

    <br><hr><br>

    <label>
        <textarea name="unencoded" rows="12" cols="80" style="<?= $unencoded ? 'background-color:#efe' : ''?>"><?= $unencoded ?></textarea>
        <br>
    </label>
    <button  name="do-encode" value="1">Encode</button>

    <br><br><br>
    <label>
        <textarea name="encoded" rows="12" cols="80"  style="<?= $encoded ? 'background-color:#efe' : ''?>"><?= $encoded ?></textarea>
        <br>
    </label>
    <button name="do-decode" value="1">Decode</button>
    <button name="do-eval" value="1">PHP Eval</button>

    <?php if (isset($evalOutput)) { ?>
        <br><br>
        <div>PHP Output:</div>
        <div style="padding:6px;border:1px solid #aaa;display:inline-block;margin-top:6px"><?= $evalOutput ?></div>
    <?php } else if (isset($decoded) && $lastError=$decoded->getLastError()) { ?>
        <br><br>
        <div>PHP Error:</div>
        <?php
        $lastDecodedLines = $decoded->getLastDecodedLines();
        for ($i=0; $i<count($lastDecodedLines); $i++) {?>
            <pre style="margin:0"><?= ($i+1) ?> : <?= $lastDecodedLines[$i]; ?> <?php if ($lastError->getLine()===($i+1)){?><span style="color:red"> &lt;= <?= $lastError->getMessage()?><?php } ?></pre>
        <?php } ?>
    <?php } ?>
</form>
<?php
require_once("layout-render.php");