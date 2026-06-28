<?php

use Green\TomTroc\Entity\ProfileEntity;

/**
 * @var ProfileEntity $myInformations send by View
 */
?>
<main class="flex-1">
    <section class="bg-[#FAF9F7]">
        <div class="pl-[150px] pt-[90px]">
            <h1 class="font-[Playfair Display] text-[36px]">Mon compte</h1>
        </div>
        <div class="flex mt-[48px] pb-[32px] gap-[33px] justify-center">

            <div class="flex flex-col bg-white rounded-[20px] w-[547px] h-[508px] content-center">
                <div><img src="<?= $myInformations->getAvatarPath() ?>" alt="">
                    <p class="text-[#A6A6A6] text-underline">modifier</p>
                </div>
                <div>
                    <p><?= $myInformations->getUsername() ?></p>
                    <p>Membre depuis <?= $myInformations->getMemberSince() ?></p>
                    <p>BIBLIOTHEQUE</p>
                    <p><?= $myInformations->getBookCount() ?></p>
                </div>
            </div>
            <div class="flex flex-col bg-white rounded-[20px] w-[547px] h-[508px] content-center">
                <h2>Vos informations personnelles</h2>
                <form action="/my-profile" method="POST" class="flex flex-col">
                    <label for="email">Addresse email</label>
                    <input type="text" name="profil-email" id="profil-email" placeholder="<?= $myInformations->getEmail() ?>">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="profil-password" id="profil-password" placeholder="***********">
                    <label for="username">Pseudo</label>
                    <input type="text" name="profil-username" id="profil-username" placeholder="<?= $myInformations->getUsername() ?>">
                    <button class=" w-[322px] h-[63px] bg-[#F5F3EF] border-[#00AC66] border-[1px] text-[#00AC66] rounded-[10px]">Enregistrer</button>
                </form>

            </div>
        </div>
    </section>
    <section class="bg-[#FAF9F7]">
        <div class="flex justify-center pt-[32px] pr-[150px] pl-[150px]">
            <table>
                <thead class="bg-white h-[53px]">
                    <tr class="mt-[33px]">
                        <td class="pl-[40px] pr-[40px]">PHOTO</td>
                        <td class="pl-[40px] pr-[40px]">TITRE</td>
                        <td class="pl-[40px] pr-[40px]">AUTEUR</td>
                        <td class="pl-[40px] pr-[40px]">DESCRIPTION</td>
                        <td class="pl-[40px] pr-[40px]">DISPONIBILITE</td>
                        <td class="pl-[40px] pr-[40px]">ACTION</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myInformations->getBooks() as $book): ?>
                        <tr class="h-[130px]">
                            <td><img src="<?= $book->getImagePath() ?>" alt="" class="size-[78px]"></td>
                            <td><?= $book->getTitle() ?></td>
                            <td><?= $book->getAuthor() ?></td>
                            <td><?= $book->getDescription() ?></td>
                            <td><?= $book->getAvailability()->value ?></td>
                            <td><a href="/book-edit?id=<?= $book->getId() ?>">modifier</a>
                                <a href="/my-profile">supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
</main>