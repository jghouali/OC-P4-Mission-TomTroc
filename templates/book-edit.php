<?php

use Green\TomTroc\Entity\BookEntity;

/**
 * @var BookEntity $book send by View
 */
?>
<main class="flex-1">
    <section class="bg-background-light pb-[80px] sm:pb-[92px]">
        <div>
            <p class="pl-[20px] sm:pl-[150px] pb-[16px] sm:pb-[28px] pt-[54px] sm:pt-[40px] font-inter font-normal text-grey text-[14px] tracking-normal leading-none font-light">
                <a href="/my-profile"><img class="inline" src="/images/icon-back.png" alt="Icône retour"> retour</a>
            </p>
        </div>
        <h1 class="font-playfair font-normal tracking-normal leading-[1.3] text-[30px] sm:text-[26px] text-black pb-[40px] sm:pb-[28px] pl-[20px] sm:pl-[150px] w-[271px] sm:w-auto">Modifier les informations</h1>

        <div class="bg-white sm:mx-[150px] px-[20px] sm:px-[50px] pt-[42px] sm:pt-[56px] rounded-[20px]">
            <div class="flex flex-col items-center sm:flex-row gap-[32px] sm:gap-[118px]">

                <div class="self-start">
                    <p class="pb-[8px] sm:pb-[11px] font-inter font-normal tracking-normal text-[11px] sm:text-[14px] text-grey">Photo</p>
                    <img src="<?= $book->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $book->securePrintText($book->getTitle()) ?>" class="size-[335px] sm:size-[488px]">
                    <p id="modifyImage" class="text-dark text-[16px] sm:text-[12px] text-end underline sm:pb-[48px] pt-[23px] cursor-pointer">Modifier la photo</p>
                </div>

                <div class="">
                    <form action="/book-edit" method="POST" enctype="multipart/form-data" class="flex flex-col w-full">
                        <input type="hidden" name="bookId" value="<?= $book->getId() ?>">
                        <input
                            type="file"
                            id="book-edit-imagePath"
                            name="imagePath"
                            accept=".png, image/png"
                            class="hidden" />

                        <label for="book-edit-title" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Titre</label>
                        <input type="text" name="title" id="book-edit-title" class="w-[335px] sm:w-[435px] h-[50px] mb-[32px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]" value="<?= $book->securePrintText($book->getTitle()) ?>">
                        <label for="book-edit-author" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Auteur</label>
                        <input type="text" name="author" id="book-edit-author" class="w-[335px] sm:w-[435px] h-[50px] mb-[32px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]" value="<?= $book->securePrintText($book->getAuthor()) ?>">
                        <label for="book-edit-description" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Commentaire</label>
                        <textarea name="description" id="book-edit-description" class="w-[335px] sm:w-[435px] h-[433px] sm:h-[356px] mb-[32px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]" rows="23"><?= strip_tags($book->securePrintText($book->getDescription())) ?></textarea>
                        <label for="book-edit-availability" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Disponibilité</label>
                        <select name="availability" id="book-edit-availability" class="w-[335px] sm:w-[435px] h-[50px] mb-[44px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]">
                            <option value="AVAILABLE" <?= ($book->getAvailability()->value === 'AVAILABLE') ? 'selected' : '' ?>>Disponible</option>
                            <option value="NOT-AVAILABLE" <?= ($book->getAvailability()->value === 'NOT-AVAILABLE') ? 'selected' : '' ?>>Non disponible</option>
                        </select>
                        <button class="sm:w-[322px] h-[63px]  mb-[47px] sm:mb-[68px] pxfont-inter font-semibold leading-none tracking-normal text-[16px] text-center text-white bg-primary duration-300 ease-in-out hover:bg-primary-hover rounded-[10px]">Valider</button>
                    </form>

                </div>
            </div>
        </div>
    </section>
</main>
<script>
    document.querySelector("#modifyImage").addEventListener('click', (ev) => {
        document.querySelector('#book-edit-imagePath').click();
    })
</script>