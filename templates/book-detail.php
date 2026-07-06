<?php

use Green\TomTroc\Entity\BookEntity;

/**
 * @var BookEntity $book send by View
 */
?>
<main class="flex-1">

    <?php if (isset($updateIsOk)): ?>

        <?php if (!$updateIsOk): ?>
            <div class="fixed right-1 top-[25px] ml-[20px] md:ml-[85px] mt-[40px] loginError bg-reddanger w-[300px] rounded-[20px]" id="loginError">
                <p class="text-white text-center">Une Erreur est survenue lors de l'enregistrement</p>
            </div>
        <?php else: ?>
            <div class="fixed right-1 top-[25px] ml-[20px] md:ml-[85px] mt-[40px] loginError bg-green-400 w-[300px] rounded-[20px]" id="loginError">
                <p class="text-white text-center">Le livre a bien été enregistré</p>
            </div>
        <?php endif ?>
        <script>
            const notification = document.querySelector('#loginError');

            if (notification) {
                setTimeout(() => {
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
        </script>
    <?php endif ?>

    <section class="bg-light-grey2">
        <div>
            <p class="hidden sm:block pl-[150px] pb-[16px] pt-[16px] font-inter text-grey text-[10px] tracking-normal leading-none font-light">Nos livres > <?= $book->securePrintText($book->getTitle()) ?></p>
        </div>

        <div class="flex flex-col w-full sm:flex-row sm:min-h-[863px] items-stretch">

            <div class="relative h-[449px] sm:min-h-[863px] sm:h-auto sm:w-1/2 overflow-hidden">
                <img src="<?= $book->securePrintText($book->getImagePath()) ?>" alt="Couverture du livre <?= $book->securePrintText($book->getTitle()) ?>" class="absolute w-full h-full object-cover">
            </div>

            <div class="w-full sm:w-1/2 bg-background-light">

                <div class="pl-[20px] pr-[20px] xl:pl-[85px] pt-[40px] sm:pt-[59px]">
                    <h1 class="pb-[40px] sm:pb-[24px] font-playfair font-normal text-[30px] sm:text-[36px] tracking-normal leading-none text-black"><?= $book->securePrintText($book->getTitle()) ?></h1>
                    <p class="pb-[32px] font-inter font-normal text-[16px] tracking-normal leading-none text-grey">par <?= $book->securePrintText($book->getAuthor()) ?></p>
                    <img src="/images/Line-3.png" alt="un ligne de démarcation" class="pb-[32px] ">
                    <p class="pb-[16px] font-inter font-semibold text-[8px] tracking-[0.08em] leading-none text-dark">DESCRIPTION</p>
                    <p class="max-w-[334px] md:max-w-[485px] pr-[2px] pb-[40px] sm:pb-[32px] font-inter font-normal text-[14px] tracking-normal leading-[1.2] text-dark"><?= $book->securePrintText($book->getDescription()) ?></p>
                    <p class="pb-[16px] font-inter font-semibold text-[8px] tracking-[0.08em] leading-none text-dark">PROPRIÉTAIRE</p>
                    <a href="/profile?memberId=<?= $book->getFromMember()->getId() ?>">
                        <div class="rounded-full w-[157px] h-[60px] bg-white">
                            <div class="flex flex-row items-center gap-[12px] py-[6px] pl-[6px] hover:[&>div>#blur]:bg-[rgba(0,172,103,0.7)] hover:[&>div>p]:text-primary">
                                <div>
                                    <img src="<?= $book->securePrintText($book->getFromMember()->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $book->securePrintText($book->getAuthor()) ?>" class="size-[48px] rounded-full block">
                                    <div id="blur" class="w-[48px] h-[48px] relative top-[-48px] rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-inter font-normal text-[14px] tracking-normal leading-none text-dark relative top-[-24px]"><?= $book->securePrintText($book->getFromMember()->getUsername()) ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
                    <form id="sendMessageForm" action="/my-box" method="get">
                        <input type="hidden" name="toUser" value="<?= $book->securePrintText($book->getFromMember()->getId()) ?>">
                        <button id="sendMessageButton" data-user-id="<?= $book->securePrintText($book->getFromMember()->getId()) ?>" class="font-inter font-semibold leading-none tracking-normal text-[16px] text-center text-white w-full md:w-[230px]  xl:w-[485px] h-[63px] bg-primary duration-300 ease-in-out hover:bg-primary-hover rounded-[10px] mb-[80px] mt-[40px] xl:mt-[80px]">Envoyer un message</button>
                    </form>
                </div>

            </div>
        </div>
    </section>
</main>