<?php

use Green\TomTroc\Entity\ProfileEntity;

/**
 * @var ProfileEntity $profile send by View
 */
?>
<main class="flex-1">
    <section class="bg-background-light flex flex-col gap-[33px] sm:gap-[28px] sm:flex-row px-[20px] xl:pl-[150px] xl:pr-[150px] pb-[43px] sm:pb-[214px] pt-[73px] sm:pt-[103px]">

        <div class="flex flex-col sm:flex-row gap-[28px] justify-center">
            <div class="flex flex-col sm:w-[341px] bg-white rounded-[20px] content-center">
                <div class="flex flex-col items-center pt-[48px]">

                    <div class="size-[135px] mb-[70px] rounded-full overflow-hidden">
                        <img src="<?= $profile->securePrintText($profile->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $profile->securePrintText($profile->getUsername()) ?>" class="object-cover size-[135px]">
                    </div>
                    <img src="/images/Line-242.png" alt="une ligne de démarcation" class="pb-[48px]">
                    <h1 class="font-playfair font-normal tracking-normal leading-none text-[24px] text-dark pb-[16px]"><?= $profile->securePrintText($profile->getUsername()) ?></h1>
                    <p class="font-inter font-normal tracking-normal leading-none text-[14px] text-grey pb-[21px]">Membre depuis <?= $profile->getMemberSince() ?></p>
                    <p class="font-inter font-semibold tracking-[0.08em] leading-none text-[8px] text-dark pb-[8px]">BIBLIOTHEQUE</p>
                    <p class="font-inter font-normal tracking-normal leading-none text-[14px] text-dark"><img src="/images/icon-books.png" alt="Icône nombre de livres" class="inline-block"> <?= $profile->securePrintText($profile->getBookCount()) ?></p>

                    <form id="sendMessageForm" action="/my-box" method="get">
                        <input type="hidden" name="toUser" value="<?= $profile->getId() ?>">
                        <button id="sendMessageButton" data-user-id="<?= $profile->getId() ?>" class="ml-[55px] mr-[63px] sm:ml-[63px] mt-[45px] mb-[56px] w-[215px] h-[63px] bg-[#F5F3EF] border-primary border text-primary rounded-[10px] duration-300 ease-in-out hover:text-white hover:bg-primary-hover">Envoyer un message</button>
                    </form>
                </div>
            </div>
            <div class="hidden sm:block">
                <table class="w-[771px]">
                    <thead class="bg-white h-[53px]">
                        <tr class="mt-[33px] font-inter font-semibold tracking-[0.08em] leading-none text-[8px] text-dark border-b-[1px] border-b-light-grey2">
                            <td class="w-[220px] pt-[30px] rounded-tl-[20px] pl-[66px]">PHOTO</td>
                            <td class="w-[180px] pt-[30px]">TITRE</td>
                            <td class="w-[170px] pt-[30px]">AUTEUR</td>
                            <td class="w-[150px] pt-[30px] rounded-tr-[20px]">DESCRIPTION</td>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:last-child>td:first-child]:rounded-bl-[20px] [&>tr:last-child>td:last-child]:rounded-br-[20px] [&_td]:pr-[64px] [&_td]:pt-[26px] [&_td]:pb-[34px]">
                        <?php if (count($profile->getBooks()) > 0): ?>
                            <?php foreach ($profile->getBooks() as $book): ?>
                                <tr class="h-[130px] odd:bg-white even:bg-greyblue">
                                    <td class="pl-[66px]">
                                        <img src="<?= $profile->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $profile->securePrintText($book->getTitle()) ?>" class="size-[78px]">
                                    </td>
                                    <td>
                                        <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= $profile->securePrintText($book->getTitle()) ?></p>
                                    </td>
                                    <td>
                                        <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= $profile->securePrintText($book->getAuthor()) ?></p>
                                    </td>
                                    <td>
                                        <p class="font-inter font-normal italic tracking-normal leading-none text-[12px] text-dark w-[128px] h-[62px]"><?= $profile->securePrintText($book->getDescription(), 85) ?></p>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr class="h-[130px]">
                                <td>---</td>
                                <td>---</td>
                                <td>---</td>
                                <td>---</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex sm:hidden flex-col flex-wrap content-center gap-[15px]">
            <?php foreach ($profile->getBooks() as $book): ?>
                <div class="flex flex-col content-center items-center justify-center gap-[21px] w-[333px] h-[251px] bg-white border-[1px] border-light-grey2 rounded-[20px] ">
                    <a href="/book-detail?bookId=<?= $book->getId() ?>">
                        <div class="flex flex-row justify-center ">
                            <div class="size-[79px]">
                                <img src="<?= $profile->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $profile->securePrintText($book->getTitle()) ?>" class="size-[79px] object-cover">
                            </div>

                            <div class="pl-[18px] self-center">
                                <p class="font-inter font-normal leading-none tracking-normal text-[14px] text-black"><?= $profile->securePrintText($book->getTitle()) ?></p>
                                <p class="font-inter font-normal leading-none tracking-normal text-[14px] text-dark"><?= $profile->securePrintText($book->getAuthor()) ?></p>
                            </div>
                        </div>
                        <p class="pt-[21px] font-inter font-normal italic tracking-normal leading-none text-[14px] text-dark w-[220px] h-[62px]"><?= $profile->securePrintText($book->getDescription(), 97) ?></p>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </section>
</main>

<script>
    document.querySelector('button').addEventListener('click', (ev) => {
        sessionStorage.setItem('selectedUserId', ev.target.dataset.userId);
        location.href = '/my-box';
    })
</script>