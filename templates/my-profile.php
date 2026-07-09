<?php

use Green\TomTroc\Entity\ProfileEntity;

/**
 * @var ProfileEntity $myInformations send by View
 */
?>
<main class="flex-1">
    <section class="px-[20px] bg-background-light grid xl:pl-[150px] xl:pr-[150px] pb-[62px] sm:pb-[102px]">
        <div class=""></div>
        <div class="pl-[20px] pt-[78px] sm:pl-[150px] sm:pt-[90px]">
            <h1 class="font-playfair font-normal tracking-normal leading-none text-[30px] sm:text-[26px] text-black pb-[40px] sm:pb-[48px]">Mon compte</h1>
        </div>

        <div class="flex flex-col sm:flex-row pb-[32px] gap-[33px] justify-center">
            <div class="flex flex-col flex-1/2 xl:w-[547px] bg-white rounded-[20px] content-center">
                <div class="flex flex-col items-center pt-[48px] pb-[36px] sm:pb-[93px]">

                    <div class="size-[135px] rounded-full overflow-hidden">
                        <img src="<?= $myInformations->securePrintText($myInformations->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $myInformations->securePrintText($myInformations->getUsername()) ?>" class="object-cover size-[135px]">
                    </div>
                    <p id="modifyAvatar" class="text-grey underline pb-[48px] cursor-pointer">modifier</p>
                    <img src="/images/Line-242.png" alt="une ligne de démarcation" class="pb-[48px]">
                    <p class="font-playfair font-normal tracking-normal leading-none text-[24px] text-dark pb-[11px]"><?= $myInformations->securePrintText($myInformations->getUsername()) ?></p>
                    <p class="font-inter font-normal tracking-normal leading-none text-[14px] text-grey pb-[21px]">Membre depuis <?= $myInformations->getMemberSince() ?></p>
                    <p class="font-inter font-semibold tracking-[0.08em] leading-none text-[8px] text-dark pb-[6px]">BIBLIOTHEQUE</p>
                    <p class="font-inter font-normal tracking-normal leading-none text-[14px] text-dark"><img src="/images/icon-books.png" alt="icône nombre de livre" class="inline-block"> <?= $myInformations->securePrintText($myInformations->getBookCount()) ?></p>
                    <p id="addBook" class="text-grey underline pb-[48px] cursor-pointer"><a href="book-add">ajouter un livre</a></p>
                </div>
            </div>

            <div class="flex flex-col flex-1/2 xl:w-[555px] bg-white rounded-[20px] pt-[40px] sm:pt-[36px] items-center content-center">
                <div>
                    <p class="pb-[22px] sm:pb-[26px] font-inter font-normal leading-none tracking-normal text-[16px] text-dark">Vos informations personnelles</p>
                    <form action="/my-profile" method="POST" enctype="multipart/form-data" class="flex flex-col">
                        <label for="profil-avatarPath" class="sr-only">Téléverser un avatar</label>
                        <input
                            type="file"
                            id="profil-avatarPath"
                            name="avatarPath"
                            accept=".png, image/png"
                            class="hidden">

                        <label for="profil-email" class="pb-[10px] font-inter font-normal leading-none tracking-normal text-[14px] text-grey">Addresse email</label>
                        <input type="text" name="email" id="profil-email" placeholder="<?= $myInformations->securePrintText($myInformations->getEmail()) ?>" class="bg-greyblue rounded-[6px] border-light-grey focus:border-light-grey border w-[269px] sm:w-[200px] lg:w-[322px] h-[50px] placeholder:font-inter placeholder:text-[14px] placeholder:text-dark px-[14px] outline-none focus:outline-none text-dark">
                        <label for="profil-password" class="pt-[32px] pb-[10px] font-inter font-normal leading-none tracking-normal text-[14px] text-grey">Mot de passe</label>
                        <input type="password" name="password" id="profil-password" placeholder="•••••••••" class="bg-greyblue rounded-[6px] border-light-grey focus:border-light-grey border w-[269px] sm:w-[200px] lg:w-[322px] h-[50px] placeholder:font-inter placeholder:text-[14px] placeholder:text-dark px-[14px] outline-none focus:outline-none text-dark">
                        <label for="profil-username" class="pt-[32px] pb-[10px] font-inter font-normal leading-none tracking-normal text-[14px] text-grey">Pseudo</label>
                        <input type="text" name="username" id="profil-username" placeholder="<?= $myInformations->securePrintText($myInformations->getUsername()) ?>" class="bg-greyblue rounded-[6px] border-light-grey focus:border-light-grey border w-[269px] sm:w-[200px] lg:w-[322px] h-[50px] placeholder:font-inter placeholder:text-[14px] placeholder:text-dark px-[14px] outline-none focus:outline-none text-dark">
                        <button class="mt-[32px] mb-[48px] sm:mb-[37px] w-[269px] sm:w-[150px] h-[63px] bg-[#F5F3EF] border-primary border-[1px] text-primary rounded-[10px]">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="hidden xl:flex justify-center">
            <table class="w-[1140px]">
                <thead class="bg-white h-[53px]">
                    <tr class="mt-[33px] font-inter font-semibold tracking-[0.08em] leading-none text-[8px] text-dark border-b-[1px] border-b-light-grey2">
                        <td class="w-[220px] rounded-tl-[20px] pl-[66px]">PHOTO</td>
                        <td class="w-[180px]">TITRE</td>
                        <td class="w-[170px]">AUTEUR</td>
                        <td class="w-[150px]">DESCRIPTION</td>
                        <td class="w-[150px]">DISPONIBILITE</td>
                        <td class="w-[150px] rounded-tr-[20px]">ACTION</td>
                    </tr>
                </thead>
                <tbody class="[&>tr:last-child>td:first-child]:rounded-bl-[20px] [&>tr:last-child>td:last-child]:rounded-br-[20px] [&_td]:pr-[78px] [&_td]:pt-[36px] [&_td]:pb-[32px]">
                    <?php if (count($myInformations->getBooks()) > 0): ?>
                        <?php foreach ($myInformations->getBooks() as $book): ?>
                            <tr class="h-[130px] odd:bg-white even:bg-greyblue">
                                <td class="pl-[66px]">
                                    <img src="<?= $myInformations->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $myInformations->securePrintText($book->getTitle()) ?>" class="size-[78px]">
                                </td>
                                <td>
                                    <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= $myInformations->securePrintText($book->getTitle()) ?></p>
                                </td>
                                <td>
                                    <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= $myInformations->securePrintText($book->getAuthor()) ?></p>
                                </td>
                                <td>
                                    <p class="font-inter font-normal italic tracking-normal leading-none text-[12px] text-dark w-[128px] h-[62px]"><?= $myInformations->securePrintText($book->getDescription(), 85) ?></p>
                                </td>
                                <td>
                                    <div class="font-inter font-medium leading-none tracking-normal text-[8px] text-center text-white w-[71px] h-[18px] pt-[5px] px-[13px] rounded-[30px] <?= ($book->getAvailability()->value === 'AVAILABLE') ? 'bg-greendispo' : 'bg-red' ?>"><?= ($book->getAvailability()->value === 'AVAILABLE') ? 'disponible' : 'non dispo.' ?></div>
                                </td>
                                <td>
                                    <p class="font-inter font-normal tracking-normal leading-none w-[125px] text-[12px] text-dark">
                                        <a href="/book-edit?bookId=<?= $book->getId() ?>" class="underline mr-[28px]">Éditer</a>
                                        <a href="/book-delete?bookId=<?= $book->getId() ?>" class="underline text-reddanger">Supprimer</a>
                                    </p>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr class="h-[130px] odd:bg-white even:bg-greyblue">
                            <td class="pl-[66px]">
                                ---
                            </td>
                            <td>
                                <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark">---</p>
                            </td>
                            <td>
                                <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark">---</p>
                            </td>
                            <td>
                                <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark">---</p>
                            </td>
                            <td>
                                <div class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark">---</div>
                            </td>
                            <td>
                                <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark">---</p>
                            </td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>

        <div class="flex xl:hidden flex-col flex-wrap content-center gap-[15px]">
            <?php foreach ($myInformations->getBooks() as $book): ?>
                <div class="flex flex-col content-center items-center justify-center gap-[21px] w-[333px] h-[327px] bg-white border-[1px] border-light-grey2 rounded-[20px] ">
                    <a href="/book-detail?bookId=<?= $book->getId() ?>">
                        <div class="flex flex-row justify-center ">
                            <div class="size-[79px]">
                                <img src="<?= $myInformations->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $myInformations->securePrintText($book->getTitle()) ?>" class="size-[79px] object-cover">
                            </div>

                            <div class="pl-[18px]">
                                <p class="font-inter font-normal leading-none tracking-normal text-[14px] text-black"><?= $myInformations->securePrintText($book->getTitle()) ?></p>
                                <p class="pb-[16px] font-inter font-normal leading-none tracking-normal text-[14px] text-dark"><?= $myInformations->securePrintText($book->getAuthor()) ?></p>
                                <div class="font-inter font-medium leading-none tracking-normal text-[8px] text-center text-white w-[71px] h-[18px] pt-[5px] px-[13px] rounded-[30px] <?= ($book->getAvailability()->value === 'AVAILABLE') ? 'bg-greendispo' : 'bg-red' ?>"><?= ($book->getAvailability()->value === 'AVAILABLE') ? 'disponible' : 'non dispo.' ?></div>
                            </div>
                        </div>
                        <p class="pt-[21px] font-inter font-normal italic tracking-normal leading-none text-[14px] text-dark w-[220px] h-[62px]"><?= $myInformations->securePrintText($book->getDescription(), 97) ?></p>
                    </a>
                    <div>
                        <p class="pt-[43px] font-inter font-normal tracking-normal leading-none text-[16px] text-dark">
                            <a href="/book-edit?bookId=<?= $book->getId() ?>" class="underline mr-[43px]">Éditer</a>
                            <a href="/book-delete?bookId=<?= $book->getId() ?>" class="underline text-reddanger">Supprimer</a>
                        </p>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

    </section>
</main>
<script>
    document.querySelector("#modifyAvatar").addEventListener('click', (ev) => {
        document.querySelector('#profil-avatarPath').click();
    })
</script>