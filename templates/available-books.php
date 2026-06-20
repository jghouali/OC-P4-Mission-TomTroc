<?php

/**
 * @var array $data send by View
 */
?>
<main>
    <?php foreach ($data['avalaibleBooks'] as $book): ?>
        <?= $book['title'] ?>
    <?php endforeach ?>
</main>