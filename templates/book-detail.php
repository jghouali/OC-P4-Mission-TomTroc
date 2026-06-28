<?php

use Green\TomTroc\Entity\BookEntity;

/**
 * @var BookEntity $book send by View
 */
?>
<main class="flex-1">
    <section class="bg-[#F5F3EF]">
        <div>
            <p class="pl-[150px] pb-[16px] pt-[16px] font-inter font-normal text-grey text-[10px] tracking-normal leading-none font-light">Nos livres > <?= $book->getTitle() ?></p>
        </div>

        <div class="flex flex-col sm:flex-row">

            <div class="max-h-[449px] sm:max-h-[720px] basis-1/2">
                <img src="<?= $book->getImagePath() ?>" alt="" class="w-full sm:max-h-[720px] object-center sm:object-cover">
            </div>

            <div class="basis-1/2 bg-background-light">
                <div class="pl-[20px] pr-[20px] md:pl-[85px] pt-[40px] sm:pt-[59px]">
                    <h1 class="pb-[40px] sm:pb-[17px] font-playfair font-normal text-[30px] sm:text-[36px] tracking-normal leading-none text-black"><?= $book->getTitle() ?></h1>
                    <p class="pb-[32px] font-inter font-normal text-[16px] tracking-normal leading-none text-grey">par <?= $book->getAuthor() ?></p>
                    <img src="/images/Line-3.png" alt="" class="pb-[32px] ">
                    <p class="pb-[16px] font-inter font-semibold text-[8px] tracking-[0.08em] leading-none text-dark">DESCRIPTION</p>
                    <p class="pb-[40px] sm:pb-[32px] font-inter font-normal text-[14px] tracking-normal leading-none text-dark"><?= $book->getDescription() ?></p>
                    <p class="pb-[16px] font-inter font-semibold text-[8px] tracking-[0.08em] leading-none text-dark">PROPRIÉTAIRE</p>
                    <div class="rounded-full w-[157px] h-[60px] bg-white">
                        <div class="flex flex-row items-center gap-[12px] py-[6px] pl-[6px]">
                            <div><img src="<?= $book->getFromMember()->getAvatarPath() ?>" alt="" class="size-[48px] rounded-full"></div>
                            <div>
                                <p class="font-inter font-normal text-[14px] tracking-normal leading-none text-dark"><?= $book->getFromMember()->getUsername() ?></p>
                            </div>
                        </div>
                    </div>
                    <a href="/available-books"><button class="font-inter font-semibold leading-none tracking-normal text-[16px] text-center text-white w-full md:w-[230px]  xl:w-[485px] h-[63px] bg-primary duration-300 ease-in-out hover:bg-primary-hover rounded-[10px] mt-[80px]">Envoyer un message</button></a>
                </div>

            </div>
        </div>
    </section>
</main>