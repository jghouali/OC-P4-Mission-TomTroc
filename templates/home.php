<?php

/**
 * @var array $data send by View
 */
?>
<main>
    <?php foreach ($data['recent_book'] as $book): ?>
        <?= $book['title'] ?>
    <?php endforeach ?>
</main>