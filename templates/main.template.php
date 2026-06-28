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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');
    </style>
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->
    <!-- <script src="/js/browser@4.js"></script> -->
    <link rel="stylesheet" type="text/css" href='css/style.css'>
    <title><?= $title ?></title>
</head>


<body class="flex flex-col min-h-screen">
    <?= $header ?>

    <?= $content ?>

    <?= $footer ?>
</body>

</html>