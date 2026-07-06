<?php

/**
 * @var array $avalaibleBooks Available books send by View
 */
?>
<main class="flex-1 scrollbar-gutter-stable">
    <section class="bg-background-light pb-[55px] sm:pb-[80px]">
        <div class="pr-[20px] sm:pr-[263px] pl-[20px] sm:pl-[263px]">
            <div class="pt-[55px] sm:pt-[130px] mb-[40px] sm:mb-[72px] gap-[28px] flex flex-col lg:flex-row justify-between items-left">
                <div class="sm:w-[200px] lg:w-[343px]">
                    <h1 class="sm:w-[200px] lg:w-[343px] font-playfair text-left text-[30px] sm:text-[36px]">Nos livres à l’échange</h1>
                </div>
                <div class="flex items-center gap-[12px] bg-white border border-light-grey rounded-[6px] w-full max-w-sm h-[50px] sm:w-[322px] px-[12px]">
                    <img src="/images/search.png" alt="Icône rechercher" class="w-4 h-4 text-grey">
                    <form action="/available-books" method="get">
                        <input type="text" name="search" placeholder="Rechercher un livre" id="book-search" class="outline-none flex-1 bg-white border-none border-light-grey placeholder:font-inter placeholder:text-[14px] placeholder:text-grey">
                    </form>
                </div>
            </div>
            <div class=" flex-1 flex flex-row gap-[15px] sm:gap-[38px] justify-center content-center flex-wrap">
                <?php foreach ($avalaibleBooks as $book): ?>
                    <a href="/book-detail?bookId=<?= $book->getId() ?>">
                        <div class="flex flex-col items-left w-[160px] sm:w-[200px] sm:h-[324px] shadow-[0px_4px_24px_0px_rgba(0,0,0,0.01)]">
                            <div class="size-[160px] sm:size-[200px]">
                                <img src="<?= $book->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $book->securePrintText($book->getTitle()) ?>" class="size-[160px] sm:size-[200px] object-cover">
                                <?php if ($book->getAvailability()->value === 'NOT-AVAILABLE'): ?>
                                    <div class="relative top-[-184px] left-[118px] font-inter font-medium leading-none tracking-normal text-[8px] text-center text-white w-[71px] h-[18px] pt-[5px] px-[13px] rounded-[30px] <?= ($book->getAvailability()->value === 'AVAILABLE') ? 'bg-greendispo' : 'bg-red' ?>"><?= ($book->getAvailability()->value === 'AVAILABLE') ? 'disponible' : 'non dispo.' ?></div>
                                <?php endif ?>
                            </div>

                            <div class="pt-[16px] sm:pt-[20px] pb-[19px] sm:pb-[23px] pl-[11px] sm:pl-[14px] rounded-b-[15px] bg-white">
                                <p class="font-inter font-normal leading-none tracking-normal text-[13px] sm:text-[16px] text-black"><?= mb_strimwidth($book->securePrintText($book->getTitle()), 0, 22, '...') ?></p>
                                <p class="pt-[7px] sm:pt-[8px] font-inter font-normal leading-none tracking-normal text-[11px] sm:text-[14px] text-grey"><?= mb_strimwidth($book->securePrintText($book->getAuthor()), 0, 20, '...') ?></p>
                                <p class="pt-[19px] sm:pt-[22px] font-inter italic font-normal leading-none tracking-normal text-[8px] sm:text-[10px] text-grey"><em>Vendu par : <?= mb_strimwidth($book->securePrintText($book->getFromMember()->getUsername()), 0, 20, '...') ?></em></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </section>
</main>