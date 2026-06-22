<?php

/**
 * @var string $username send by View
 * @var string $avatarPath send by View
 * @var string $memberSince send by View
 * @var string $bookCount send by View
 * @var array $books send by View
 */
?>
<main>
    <?= $username ?>
    <?= $avatarPath ?>
    Membre depuis <?= $memberSince ?>
    <?= $bookCount ?>
    <?php foreach ($books as $book): ?>
        <?= $book->getImagePath() ?>
        <?= $book->getTitle() ?>
        <?= $book->getAuthor() ?>
        <?= $book->getDescription() ?>
        <?= $book->getAvailability()->value ?>
    <?php endforeach ?>
</main>