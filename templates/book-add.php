<?php

use Green\TomTroc\Entity\MemberEntity;

/**
 * @var MemberEntity $member send by View
 */
?>
<main class="flex-1">
    <section class="bg-background-light pb-[80px] sm:pb-[92px]">
        <div>
            <p class="pl-[20px] sm:pl-[150px] pb-[16px] sm:pb-[28px] pt-[54px] sm:pt-[40px] font-inter font-normal text-grey text-[14px] tracking-normal leading-none font-light">
                <a href="/my-profile"><img class="inline" src="/images/icon-back.png" alt="Icône retour"> retour</a>
            </p>
        </div>
        <h1 class="font-playfair font-normal tracking-normal leading-[1.3] text-[30px] sm:text-[26px] text-black pb-[40px] sm:pb-[28px] pl-[20px] sm:pl-[150px] w-[271px] sm:w-auto">Ajouter un livre</h1>

        <div class="bg-white sm:mx-[150px] px-[20px] sm:px-[50px] pt-[42px] sm:pt-[56px] rounded-[20px]">
            <div class="flex flex-col items-center sm:flex-row gap-[32px] sm:gap-[118px]">

                <div class="self-start">
                    <p class="pb-[8px] sm:pb-[11px] font-inter font-normal tracking-normal text-[11px] sm:text-[14px] text-grey">Photo</p>
                    <img src="/upload/books/default-book.png" alt="Couverture du livre par default" class="size-[335px] sm:size-[488px]">
                    <p id="modifyImage" class="text-dark text-[16px] sm:text-[12px] text-end underline sm:pb-[48px] pt-[23px] cursor-pointer">Modifier la photo</p>
                </div>

                <div class="">
                    <form action="/book-add" method="POST" enctype="multipart/form-data" class="flex flex-col w-full">
                        <input type="hidden" name="memberId" value="<?= $member->getId() ?>">
                        <input
                            type="file"
                            id="book-add-imagePath"
                            name="imagePath"
                            accept=".png, image/png"
                            class="hidden" />

                        <label for="book-add-title" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Titre</label>
                        <input type="text" name="title" id="book-add-title" class="w-[335px] sm:w-[435px] h-[50px] mb-[32px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]" value="">
                        <label for="book-add-author" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Auteur</label>
                        <input type="text" name="author" id="book-add-author" class="w-[335px] sm:w-[435px] h-[50px] mb-[32px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]" value="">
                        <label for="book-add-description" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Commentaire</label>
                        <textarea name="description" id="book-add-description" class="w-[335px] sm:w-[435px] h-[433px] sm:h-[356px] mb-[32px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]" rows="23"></textarea>
                        <label for="book-add-availability" class="w-[335px] sm:w-[435px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Disponibilité</label>
                        <select name="availability" id="book-add-availability" class="w-[335px] sm:w-[435px] h-[50px] mb-[44px] bg-greyblue rounded-[6px] border border-[#F0F0F0] text-dark text-[14px] p-[14px]">
                            <option value="AVAILABLE" selected>Disponible</option>
                            <option value="NOT-AVAILABLE">Non disponible</option>
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
        document.querySelector('#book-add-imagePath').click();
    })
</script>