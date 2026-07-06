<?php

use Green\TomTroc\Entity\BookEntity;

/**
 * @var BookEntity $book send by View
 */
?>
<main class="flex-1">
    <?= $book->getTitle() ?>
    <?= $book->getAuthor() ?>
    <?= $book->getDescription() ?>
</main>