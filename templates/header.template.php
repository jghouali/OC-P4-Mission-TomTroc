<?php

/**
 * @var string $title title send by View
 */
?>
<header class="flex flex-row w-full h-[56px] sm:h-[81px] bg-[#F5F3EF]">

    <div class="pl-[20px] lg:pl-[150px] content-center">
        <a href="/"><img src="/images/logo.png" alt="" class="w-[78px] sm:min-w-[155px] h-[25px] sm:h-[51px]"></a>
    </div>
    <div class="sm:hidden justify-end content-center">
        <img src="/images/menu-mobile.png" alt="" class="w-[22px] h-[15px]">
    </div>
    <nav class="pl-[20px] xl:pl-[78px] sm:pr-[20px] lg:pr-[150px] hidden sm:flex flex-col sm:flex-row w-full justify-between">
        <ul class="flex flex-col sm:flex-row justify-start items-center sm:gap-[20px] md:gap-[44px] font-inter font-normal leading-none tracking-[0.02em] text-[14px] text-dark">
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/">Accueil</a></li>
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/available-books' ? 'font-semibold' : '' ?> hover:font-semibold text-center"><a href="/available-books">Nos livres à l'échange</a></li>
        </ul>
        <ul class="flex flex-col sm:flex-row justify-end items-center sm:gap-[20px] md:gap-[58px] font-inter font-normal leading-none tracking-[0.02em] text-[14px] text-dark">
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/my-box' ? 'font-semibold' : '' ?> hover:font-semibold w-[120px]"><a href="/my-box"><img src="/images/Icon-messagerie.png" alt="" class="inline"> Messagerie <div class="inline w-[11px] h-[15px] bg-[#292929] pr-[2px] pl-[2px] text-center text-[#F5F3EF] rounded-[6px]">0</div></a></li>
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/my-profile' ? 'font-semibold' : '' ?> hover:font-semibold w-[50px]"><a href="/my-profile"><img src="/images/Icon-mon-compte.png" alt="" class="inline"> Mon compte</a></li>
            <?php if (!isset($_SESSION['id'])): ?>
                <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/login' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/login">Connexion</a></li>
            <?php endif ?>
            <?php if (isset($_SESSION['id'])): ?>
                <li class="hover:font-semibold"><a href="/logout">Deconnexion</a></li>
            <?php endif ?>
        </ul>
    </nav>
</header>