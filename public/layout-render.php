<?php
$pageContent = ob_get_clean();
if (!isset($title)) {
    $title = "Unicodez";
}
?>
<!doctype html>
<html lang="en">
<head>
    <title><?= $title ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<h1 style="margin-bottom:0"><?= $title ?></h1>
<?= $pageContent ?>
</body>
</html>
