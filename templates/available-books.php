<?php

/**
 * @var array $avalaibleBooks Available books send by View
 */
?>
<main>
    <?php foreach ($avalaibleBooks as $book): ?>
        <?= $book->getTitle() ?>
    <?php endforeach ?>
</main>