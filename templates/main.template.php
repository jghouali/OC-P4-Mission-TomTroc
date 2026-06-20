<?php

/**
 * @var string $title send by View
 * @var string $header send by View
 * @var string $content send by View
 * @var string $footer send by View
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <?= $header ?>

    <?= $content ?>

    <?= $footer ?>
</body>

</html>