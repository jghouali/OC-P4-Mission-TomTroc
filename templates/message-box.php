<?php

/**
 * @var array $messagesByUser send by View
 */
?>
<main>
    <?php foreach ($messagesByUser as $user => $messageArray): ?>
        <div>
            <div><?= $user ?></div>
            <div>
                <?php foreach ($messageArray as $message): ?>
                    <div>
                        <?= $message['sent_at'] ?>
                        <?= $message['action'] ?>
                        <?= $message['content'] ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endforeach ?>
</main>