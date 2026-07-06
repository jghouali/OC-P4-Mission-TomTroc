<?php

/**
 * @var array $data send by View
 */
?>
<main class="flex-1">
    <?php if (isset($loginError)): ?>
        <div class="loginError" id="loginError">
            <?= $loginError ?>
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
        </div>
    <?php endif ?>
    <section class="bg-background-light">
        <div class="flex flex-col md:flex-row md:gap-[30px] md:justify-between">
            <div class="pt-[78px] md:pt-[130px] w-full self-center sm:self-start sm:w-[322px] max-w-[335px] xl:max-w-[485px] md:pl-[20px] xl:pl-[150px] 2xl:pl-[400px]">
                <h1 class="font-playfair font-normal tracking-normal leading-[1.3] text-[30px] sm:text-[36px] text-black pb-[56px]">Connexion</h1>

                <form action="/login" method="post" class="flex flex-col w-full">
                    <label for="login-email" class="sm:w-[322px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Adresse email</label>
                    <input type="text" name="email" id="login-email" autocomplete="email" class="sm:w-[322px] h-[50px] mb-[32px] bg-white rounded-[6px] border-[1px] border-[#F0F0F0]">
                    <label for="login-password" class="sm:w-[322px] font-inter font-normal tracking-normal text-[14px] text-grey pb-[10px]">Mot de passe</label>
                    <input type="password" name="password" id="login-password" autocomplete="current-password" class="sm:w-[322px] h-[50px] mb-[32px] bg-white rounded-[6px] border-[1px] border-[#F0F0F0]">
                    <button class="sm:w-[322px] h-[63px] font-inter font-semibold leading-none tracking-normal text-[16px] text-center text-white bg-primary duration-300 ease-in-out hover:bg-primary-hover rounded-[10px]">Se connecter</button>
                    <p class="sm:w-[322px] font-inter font-normal tracking-normal text-[14px] text-dark mt-[40px] pb-[100px]">Pas de compte ? <a href="/register" class="underline">Inscrivez-vous</a></p>
                </form>
            </div>
            <div class="pr-0"><img src="/images/books.png" alt="pleins de livres dans une bibliothèque" class="h-full"></div>
        </div>
    </section>
</main>