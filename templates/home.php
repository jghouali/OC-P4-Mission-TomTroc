<?php

/**
 * @var array $recent_books send by View
 */
?>
<main>
    <?php foreach ($recent_books as $book): ?>
        <?= $book->getTitle() ?>
    <?php endforeach ?>
</main>