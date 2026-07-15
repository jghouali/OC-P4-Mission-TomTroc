<?php

/**
 * @var array $recent_books send by View
 */
?>
<main class="flex-1 scrollbar-gutter-stable">
    <section class="bg-light-grey2 px-[20px] pb-[48px] sm:pb-[101px] sm:pt-[80px]">
        <div class="flex flex-col-reverse sm:flex-row justify-center sm:gap-[108px] sm:justify-between lg:pl-[270px] lg:pr-[290px]">

            <div class="max-w-[335px] sm:w-[329px] mt-[32px] sm:mt-[54px] self-center">
                <h1 class="font-playfair font-normal tracking-normal leading-[1.3] text-[30px] sm:text-[36px] text-black pb-[16px]">Rejoignez nos lecteurs passionnés</h1>
                <p class="font-inter font-light leading-[1.2] tracking-normal text-[16px] text-black pl-[1px]">Donnez une nouvelle vie à vos livres en les
                    échangeant avec d'autres amoureux de la
                    lecture. Nous croyons en la magie du
                    partage de connaissances et d'histoires à
                    travers les livres. </p>
                <a href="/available-books" class="inline-flex items-center justify-center font-inter font-semibold leading-none tracking-normal text-[16px] text-white w-full bg-primary duration-300 ease-in-out hover:bg-primary-hover rounded-[10px] w-[335px] sm:w-[152px] h-[63px] mt-[32px] sm:mt-[40px]">Découvrir</a>
            </div>

            <div class="">
                <img class="w-full sm:w-[404px]" src="/images/hamza-nouasria.jpg" alt="Personne lisant un livre devant une librairie remplie de piles de livres.">
                <p class="font-inter italic font-normal leading-none tracking-normal text-[12px] text-grey text-right pt-[5px] sm:pt-[12px] pr-[26px] sm:pr-[3px]"><em>Hamza</em></p>
            </div>
        </div>
    </section>

    <section class="bg-background-light">
        <div class="pt-[48px] sm:pt-[80px] self-center">

            <div class="flex flex-col items-center pb-[80px] sm:pb-[64px]">
                <h2 class="pb-[34px] sm:pb-[80px] px-[55px] font-playfair font-normal leading-none tracking-normal text-center text-[28px] sm:text-[32px] text-dark">Les derniers livres ajoutés</h2>
                <div class="flex flex-row flex-wrap justify-center gap-[15px] sm:gap-[38px]">
                    <?php foreach ($recent_books as $book): ?>
                        <a href="/book-detail?bookId=<?= $book->getId() ?>">
                            <div class="flex flex-col items-left w-[160px] sm:w-[200px] sm:h-[324px] shadow-[0px_4px_24px_0px_rgba(0,0,0,0.01)">
                                <div class="size-[160px] sm:size-[200px]">
                                    <img src="<?= $book->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $book->securePrintText($book->getTitle()) ?>" class="size-[160px] sm:size-[200px] object-cover">
                                </div>

                                <div class="pt-[16px] sm:pt-[20px] pb-[19px] sm:pb-[23px] pl-[11px] sm:pl-[14px] rounded-b-[15px] bg-white">
                                    <p class="font-inter font-normal leading-none tracking-normal text-[13px] sm:text-[16px] text-black"><?= $book->securePrintText($book->getTitle()) ?></p>
                                    <p class="pt-[7px] sm:pt-[8px] font-inter font-normal leading-none tracking-normal text-[11px] sm:text-[14px] text-grey"><?= $book->securePrintText($book->getAuthor()) ?></p>
                                    <p class="pt-[19px] sm:pt-[22px] font-inter italic font-normal leading-none tracking-normal text-[8px] sm:text-[10px] text-grey"><em>Vendu par : <?= $book->securePrintText($book->getFromMember()->getUsername()) ?></em></p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
                <a href="/available-books" class="hidden sm:flex items-center justify-center font-inter font-semibold leading-none tracking-normal text-[16px] text-center text-white w-[218px] h-[63px] sm:mt-[38px] pt-[22px] pb-[22px] pl-[38px] pr-[38px] bg-primary rounded-[10px] duration-300 ease-in-out hover:bg-primary-hover">Voir tous les livres</a>
            </div>

        </div>
    </section>

    <section class="bg-light-grey2 pb-[48px] sm:pb-[80px]">
        <div class="flex flex-col justify-center items-center mx-[20px]">
            <div class="flex flex-col items-center pt-[48px] sm:pt-[80px] pb-[40px]">
                <h2 class="font-playfair font-normal leading-none tracking-normal text-center text-[28px] sm:text-[32px] text-dark pb-[24px]">Comment ça marche ?</h2>
                <p class="font-inter font-light leading-[1.3] tracking-normal text-center text-[14px] sm:text-[16px] text-black">Échanger des livres avec TomTroc c’est simple et<br> amusant ! Suivez ces étapes pour commencer :</p>
            </div>
            <div class="sm:flex sm:flex-col items-center w-full">
                <div class="flex flex-col flex-wrap sm:flex-row justify-center items-center gap-[16px] sm:gap-[38px]">
                    <div class="font-inter font-normal leading-[1.3em] tracking-normal text-center text-[14px] text-dark bg-white pb-[52px] sm:pb-[44px] pt-[53px] sm:pt-[44px] pl-[28px] sm:pl-[17px] pr-[26px] sm:pr-[18px] rounded-[10px] w-full sm:w-[215px] h-[139px]">
                        <p class="w-full h-full align-middle sm:h-[51px]">Inscrivez-vous gratuitement sur <br>notre plateforme.</p>
                    </div>
                    <div class="font-inter font-normal leading-[1.3em] tracking-normal text-center text-[14px] text-dark bg-white pb-[52px] sm:pb-[44px] pt-[53px] sm:pt-[44px] pl-[28px] sm:pl-[17px] pr-[26px] sm:pr-[18px] rounded-[10px] w-full sm:w-[215px] h-[139px]">
                        <p class="w-full sm:h-[51px]">Ajoutez les livres que vous souhaitez échanger à <br>votre profil.</p>
                    </div>
                    <div class="font-inter font-normal leading-[1.3em] tracking-normal text-center text-[14px] text-dark bg-white pb-[52px] sm:pb-[44px] pt-[53px] sm:pt-[44px] pl-[28px] sm:pl-[17px] pr-[26px] sm:pr-[18px] rounded-[10px] w-full sm:w-[215px] h-[139px]">
                        <p class="w-full h-[51px]">Parcourez les livres disponibles chez d'autres membres.</p>
                    </div>
                    <div class="font-inter font-normal leading-[1.3em] tracking-normal text-center text-[14px] text-dark bg-white pb-[52px] sm:pb-[44px] pt-[53px] sm:pt-[44px] pl-[28px] sm:pl-[17px] pr-[26px] sm:pr-[18px] rounded-[10px] w-full sm:w-[215px] h-[139px]">
                        <p class="w-full h-[51px]">Proposez un échange et discutez avec d'autres passionnés de lecture.</p>
                    </div>
                </div>
                <div>
                    <a href="/available-books" class="inline-flex items-center justify-center w-full sm:w-[218px] h-[63px] mt-[32px] sm:mt-[48px] pt-[22px] pb-[22px] font-inter font-semibold leading-none tracking-normal text-[16px] text-center text-primary duration-300 ease-in-out hover:text-white bg-[#F5F3EF] border-primary border-[1px] rounded-[10px] duration-300 ease-in-out hover:bg-primary-hover">Voir tous les livres</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light-grey2 pb-[196px] sm:pb-[162px]">
        <div class="bg-[url(/images/clay-banks-full.png)] sm:bg-[url(/images/clay-banks.png)] h-[425px] sm:h-[230px] w-full bg-cover bg-center bg-no-repeat"></div>
        <div class="flex flex-col justify-center items-center">
            <div>
                <h2 class="pt-[33px] sm:pt-[80px] pb-[29px] font-playfair font-normal leading-none tracking-normal text-left text-[28px] sm:text-[32px] text-dark">Nos valeurs</h2>
                <p class="font-inter font-light leading-[1.3] tracking-normal text-[14px] sm:text-[16px] text-black max-w-[332px] sm:max-w-[392px]">Chez Tom Troc, nous mettons l'accent sur le
                    partage, la découverte et la communauté. Nos
                    valeurs sont ancrées dans notre passion pour les
                    livres et notre désir de créer des liens entre les
                    lecteurs. Nous croyons en la puissance des histoires
                    pour rassembler les gens et inspirer des
                    conversations enrichissantes.
                    <br>
                    <br>
                    Notre association a été fondée avec une conviction
                    profonde : chaque livre mérite d'être lu et partagé.
                    <br>
                    <br>
                    Nous sommes passionnés par la création d'une
                    plateforme conviviale qui permet aux lecteurs de se
                    connecter, de partager leurs découvertes littéraires
                    et d'échanger des livres qui attendent patiemment
                    sur les étagères.
                    <em class="pt-[28px] sm:pt-[39px] block font-inter italic font-normal leading-none tracking-normal text-[12px] text-grey text-left">L’équipe Tom Troc</em>
                </p>
                <div class="relative">
                    <img src="/images/coeur.png" alt="une signature en coeur" class="absolute bottom-[-160px] sm:bottom-[-75px] left-[106px] sm:left-[342px]">
                </div>
            </div>

        </div>
    </section>
</main>