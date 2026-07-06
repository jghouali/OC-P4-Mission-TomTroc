<?php

/**
 * @var string $title title send by View
 */
?>
<header class="bg-light-grey2 flex flex-col">

    <div class="w-full h-[56px] sm:h-[80px] px-[20px] lg:px-[150px] flex flex-row wrap justify-between sm:justify-start items-center">

        <a href="/"><img src="/images/logo.png" alt="logo Tomtroc" class="w-[78px] sm:min-w-[155px] h-[25px] sm:h-[51px]"></a>

        <img src="/images/menu-mobile.png" alt="Icône de menu" id="menu-mobile-button" class="sm:hidden w-[22px] h-[15px] cursor-pointer">

        <nav class="w-full pl-[20px] xl:pl-[78px] hidden sm:flex flex-row flex-nowrap justify-between">
            <ul class="flex flex-row flex-nowrap justify-start items-center sm:gap-[10px] md:gap-[20px] lg:gap-[30px] xl:gap-[44px] font-inter font-normal leading-none tracking-[0.02em] text-[14px] text-dark">
                <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/">Accueil</a></li>
                <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/available-books' ? 'font-semibold' : '' ?> hover:font-semibold text-center"><a href="/available-books">Nos livres à l'échange</a></li>
            </ul>
            <ul class="flex flex-row flex-nowrap justify-end items-center sm:gap-[10px] md:gap-[20px] lg:gap-[30px] xl:gap-[58px] font-inter font-normal leading-none tracking-[0.02em] text-[14px] text-dark">
                <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/my-box' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/my-box"><img src="/images/Icon-messagerie.png" alt="Icône Messagerie" class="inline"> Messagerie <div class="inline w-[11px] h-[15px] bg-[#292929] pr-[2px] pl-[2px] text-center text-[#F5F3EF] rounded-[6px]"><?= NOTIFICATION_COUNT ?></div></a></li>
                <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/my-profile' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/my-profile"><img src="/images/Icon-mon-compte.png" alt="Icône Mon compte" class="inline"> Mon compte</a></li>
                <?php if (!isset($_SESSION['id'])): ?>
                    <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/login' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/login">Connexion</a></li>
                <?php endif ?>
                <?php if (isset($_SESSION['id'])): ?>
                    <li class="hover:font-semibold"><a href="/logout">Deconnexion</a></li>
                <?php endif ?>
            </ul>
        </nav>

    </div>


    <nav id="menu-mobile" class="w-full hidden sm:hidden flex-col">
        <ul class="flex flex-col items-center gap-[20px] font-inter font-normal leading-none tracking-[0.02em] text-[14px] text-dark">
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/">Accueil</a></li>
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/available-books' ? 'font-semibold' : '' ?> hover:font-semibold text-center"><a href="/available-books">Nos livres à l'échange</a></li>
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/my-box' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/my-box"><img src="/images/Icon-messagerie.png" alt="Icône Messagerie" class="inline"> Messagerie <div class="inline w-[11px] h-[15px] bg-[#292929] pr-[2px] pl-[2px] text-center text-[#F5F3EF] rounded-[6px]"><?= NOTIFICATION_COUNT ?></div></a></li>
            <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/my-profile' ? 'font-semibold' : '' ?> hover:font-semibold"><a href="/my-profile"><img src="/images/Icon-mon-compte.png" alt="Icône Mon compte" class="inline"> Mon compte</a></li>
            <?php if (!isset($_SESSION['id'])): ?>
                <li class="<?= (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') === '/login' ? 'font-semibold' : '' ?> hover:font-semibold pb-[20px]"><a href="/login">Connexion</a></li>
            <?php endif ?>
            <?php if (isset($_SESSION['id'])): ?>
                <li class="hover:font-semibold pb-[20px]"><a href="/logout">Deconnexion</a></li>
            <?php endif ?>
        </ul>
    </nav>

</header>

<script>
    document.getElementById('menu-mobile-button').addEventListener('click', function() {
        const menu = document.getElementById('menu-mobile');
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
    });
</script>