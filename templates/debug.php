<?php

/**
 * @var Exception $errorException-> send by View
 */
?>
<main class="flex-1">
    <section class="bg-[#F5F3EF] pb-[80px]">
        <div class="flex pt-[80px] pb-[128px] gap-[108px] justify-center">
            <p>
                Oups ! Une erreur c'est produit : <br><br>
                sur le fichier : <?= $errorException->getFile() ?> ligne n° <?= $errorException->getLine() ?><br><br>
                Trace : <br>
                <?= $errorException->getTraceAsString() ?><br><br>
                Code d'erreur : <?= $errorException->getCode() ?><br>
                Message : <?= $errorException->getMessage() ?><br>
            </p>
        </div>
    </section>
</main>