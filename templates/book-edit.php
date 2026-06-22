<?php

use Green\TomTroc\Entity\BookEntity;

/**
 * @var BookEntity $book send by View
 */
?>
<main>
    <?= $book->getTitle() ?>
    <?= $book->getAuthor() ?>
    <?= $book->getDescription() ?>
</main>