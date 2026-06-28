<?php

use Green\TomTroc\Entity\ProfileEntity;

/**
 * @var ProfileEntity $myInformations send by View
 */
?>
<main class="flex-1">
    <section class="bg-[#FAF9F7] grid xl:pl-[150px] xl:pr-[155px]">
        <div class=""></div>
        <div class="pl-[20px] pt-[78px] sm:pl-[150px] sm:pt-[90px]">
            <h1 class="font-playfair font-normal tracking-normal leading-none text-[30px] sm:text-[26px] text-black pb-[40px] sm:pb-[48px]">Mon compte</h1>
        </div>

        <div class="flex flex-col sm:flex-row pb-[32px] gap-[33px] justify-center">
            <div class="flex flex-col flex-1/2 xl:w-[547px] bg-white rounded-[20px] content-center">
                <div class="flex flex-col items-center pt-[48px] pb-[36px] sm:pb-[93px]">

                    <div class="size-[135px] rounded-full overflow-hidden">
                        <img src="<?= $myInformations->getAvatarPath() ?>" alt="">
                    </div>
                    <p class="text-[#A6A6A6] underline pb-[48px]">modifier</p>
                    <img src="/images/Line-242.png" alt="" class="pb-[48px]">
                    <p class="font-playfair font-normal tracking-normal leading-none text-[24px] text-dark pb-[11px]"><?= $myInformations->getUsername() ?></p>
                    <p class="font-inter font-normal tracking-normal leading-none text-[14px] text-grey pb-[21px]">Membre depuis <?= $myInformations->getMemberSince() ?></p>
                    <p class="font-inter font-semibold tracking-[0.08em] leading-none text-[8px] text-dark pb-[6px]">BIBLIOTHEQUE</p>
                    <p class="font-inter font-normal tracking-normal leading-none text-[14px] text-dark"><img src="/images/icon-books.png" alt="" class="inline-block"> <?= $myInformations->getBookCount() ?></p>
                </div>
            </div>

            <div class="flex flex-col flex-1/2 xl:w-[555px] bg-white rounded-[20px] pt-[40px] pl-[32px] sm:pt-[36px] sm:pl-[117px] content-center">
                <div>
                    <p class="pb-[22px] sm:pb-[26px] font-inter font-normal leading-none tracking-normal text-[16px] text-dark">Vos informations personnelles</p>
                    <form action="/my-profile" method="POST" class="flex flex-col">
                        <label for="email" class="pb-[10px] font-inter font-normal leading-none tracking-normal text-[14px] text-grey">Addresse email</label>
                        <input type="text" name="profil-email" id="profil-email" placeholder="<?= $myInformations->getEmail() ?>" class="bg-greyblue rounded-[6px] border-light-grey border-[1px] w-[269px] sm:w-[322px] h-[50px] placeholder:font-inter placeholder:text-[14px] placeholder:text-dark placeholder:pl-[14px]">
                        <label for="password" class="pt-[32px] pb-[10px] font-inter font-normal leading-none tracking-normal text-[14px] text-grey">Mot de passe</label>
                        <input type="password" name="profil-password" id="profil-password" placeholder="***********" class="bg-greyblue rounded-[6px] border-light-grey border-[1px] w-[269px] sm:w-[322px] h-[50px] placeholder:font-inter placeholder:text-[14px] placeholder:text-dark placeholder:pl-[14px]">
                        <label for="username" class="pt-[32px] pb-[10px] font-inter font-normal leading-none tracking-normal text-[14px] text-grey">Pseudo</label>
                        <input type="text" name="profil-username" id="profil-username" placeholder="<?= $myInformations->getUsername() ?>" class="bg-greyblue rounded-[6px] border-light-grey border-[1px] w-[269px] sm:w-[322px] h-[50px] placeholder:font-inter placeholder:text-[14px] placeholder:text-dark placeholder:pl-[14px]">
                        <button class="mt-[32px] mb-[48px] sm:mb-[37px] w-[269px] sm:w-[150px] h-[63px] bg-[#F5F3EF] border-[#00AC66] border-[1px] text-[#00AC66] rounded-[10px]">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="flex justify-center pt-[32px] pr-[150px] pl-[150px]">
            <table>
                <thead class="bg-white h-[53px]">
                    <tr class="mt-[33px] font-inter font-semibold tracking-[0.08em] leading-none text-[8px] text-dark border-b-[1px] border-b-light-grey2">
                        <td class="w-[185px] pl-[40px] pr-[40px] rounded-tl-[20px]">PHOTO</td>
                        <td class="w-[150px] pl-[40px] pr-[40px]">TITRE</td>
                        <td class="w-[150px] pl-[40px] pr-[40px]">AUTEUR</td>
                        <td class="w-[150px] pl-[40px] pr-[40px]">DESCRIPTION</td>
                        <td class="w-[150px] pl-[40px] pr-[40px]">DISPONIBILITE</td>
                        <td class="w-[150px] pl-[40px] pr-[40px] rounded-tr-[20px]">ACTION</td>
                    </tr>
                </thead>
                <tbody class="[&>tr:last-child>td:first-child]:rounded-bl-[20px] [&>tr:last-child>td:last-child]:rounded-br-[20px]">
                    <?php if (count($myInformations->getBooks()) > 0): ?>
                        <?php foreach ($myInformations->getBooks() as $book): ?>
                            <tr class="h-[130px] odd:bg-white even:bg-greyblue">
                                <td>
                                    <img src="<?= $book->getImagePath() ?>" alt="" class="size-[78px]">
                                </td>
                                <td>
                                    <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= $book->getTitle() ?></p>
                                </td>
                                <td>
                                    <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= $book->getAuthor() ?></p>
                                </td>
                                <td>
                                    <p class="font-inter font-normal italic tracking-normal leading-none text-[12px] text-dark"><?= mb_strimwidth($book->getDescription(), 0, 50, '...') ?></p>
                                </td>
                                <td><?= $book->getAvailability()->value ?></td>
                                <td><a href="/book-edit?id=<?= $book->getId() ?>">modifier</a>
                                    <a href="/my-profile">supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr class="h-[130px]">
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </section>
</main>