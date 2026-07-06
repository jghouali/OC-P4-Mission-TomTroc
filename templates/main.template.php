<?php

/**
 * @var string $title send by View
 * @var string $header send by View
 * @var string $content send by View
 * @var string $footer send by View
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href='css/style.css'>
    <title><?= $title ?></title>
</head>


<body class="flex flex-col min-h-screen min-w-screen">
    <?= $header ?>

    <?= $content ?>

    <?= $footer ?>
</body>

</html>