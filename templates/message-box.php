<?php

/**
 * @var array $messagesByUser send by View
 */
?>
<main class="bg-light-grey2 flex flex-col flex-1 h-full lg:px-[150px]">
    <section class="flex flex-col flex-1 pb-[42px] sm:pb-[98px]">
        <div class="flex flex-row flex-1">
            <div id="usersDiv" class="flex flex-col bg-background-light w-full sm:w-[308px] px-[20px]">
                <h1 class="font-playfair font-normal tracking-normal leading-none text-[26px] text-black sm:pl-[34px] pt-[55px] pb-[27px] sm:pb-[27px]">Messagerie</h1>
                <?php foreach ($messagesByUser as $user => $messageArray): ?>
                    <div class="selectedUser flex flex-row gap-[12px] border-b sm:border-b-0 border-white sm:pl-[34px] py-[18px] w-full sm:w-[308px] h-[84px] cursor-pointer" data-user-id="<?= $messageArray['profileObject']->getId() ?>">
                        <img src="<?= $messageArray['profileObject']->securePrintText($messageArray['profileObject']->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $messageArray['profileObject']->securePrintText($messageArray['profileObject']->getUsername()) ?>" class="size-[48px] shrink-0 rounded-full">
                        <div class="flex flex-col sm:pr-[42px] gap-[7px] justify-center flex-1">
                            <div class="flex flex-row justify-between">
                                <div class="font-inter font-normal tracking-normal leading-none text-[14px] text-dark"><?= $user ?></div>
                                <div class="font-inter font-normal tracking-normal leading-none text-[12px] text-dark"><?= (preg_match('/ [0-9][0-9]:[0-9][0-9]/', array_last($messageArray['messages'])['sent_at'], $match)) ? $match[0] : '' ?></div>
                            </div>
                            <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-grey">
                                <?= mb_strimwidth($messageArray['profileObject']->securePrintText(array_last($messageArray['messages'])['content']), 0, 30, '...') ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <div id="usersMessageDiv" class="hidden sm:flex flex-col flex-1 px-[20px] sm:pl-[44px] sm:pr-[150px]">
                <p id="usersMessageDivBack" class="sm:hidden font-inter font-normal tracking-normal leading-none text-[12px] text-grey mb-[3px] cursor-pointer">retour</p>
                <?php if (isset($toUserMember) && !key_exists($toUserMember->securePrintText($toUserMember->getUserName()), $messagesByUser)): ?>
                    <div class="messageContent flex-col flex-1 justify-between  max-h-[440px] sm:max-h-[540px] scrollbar-thin overflow-y-scroll" id="messageContent-<?= $toUserMember->getId() ?>">
                        <div class="flex flex-row items-center gap-[12px] w-[308px] h-[84px]">
                            <img src="<?= $toUserMember->securePrintText($toUserMember->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $toUserMember->securePrintText($toUserMember->getUserName()) ?>" class="size-[48px] rounded-full">
                            <div class="font-inter font-semibold tracking-normal leading-none text-[14px] text-dark"><?= $toUserMember->securePrintText($toUserMember->getUserName()) ?></div>
                        </div>
                        <div class="flex flex-col justify-end flex-1">
                        </div>
                    </div>
                <?php endif ?>
                <?php foreach ($messagesByUser as $user => $messageArray): ?>
                    <div class="messageContent <?= (isset($toUserMember) && $toUserMember->getId() == $messageArray['profileObject']->getId()) ? 'flex' : 'hidden'  ?> flex-col flex-1 justify-between  max-h-[440px] sm:max-h-[540px] scrollbar-thin overflow-y-scroll" id="messageContent-<?= $messageArray['profileObject']->getId() ?>">
                        <div class="flex flex-row items-center gap-[12px] w-[308px] h-[84px]">
                            <img src="<?= $messageArray['profileObject']->securePrintText($messageArray['profileObject']->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $messageArray['profileObject']->securePrintText($messageArray['profileObject']->getUsername()) ?>" class="size-[48px] rounded-full">
                            <div class="font-inter font-semibold tracking-normal leading-none text-[14px] text-dark"><?= $messageArray['profileObject']->securePrintText($user) ?></div>
                        </div>
                        <div class="flex flex-col justify-end flex-1">
                            <?php foreach ($messageArray['messages'] as $messages): ?>
                                <?php if ($messages['action'] === 'received'): ?>
                                    <div class="mt-[25px] max-w-[261px] sm:max-w-[500px] self-start">
                                        <div class="flex flex-row gap-[6px] items-center mb-[8px]">
                                            <img src="<?= $messageArray['profileObject']->securePrintText($messageArray['profileObject']->getAvatarPath()) ?>" alt="Avatar de l'utilisateur <?= $messageArray['profileObject']->securePrintText($messageArray['profileObject']->getUsername()) ?>" class="min-w-[24px] size-[24px] rounded-full">
                                            <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-grey min-w-[150px]"><?= (preg_match('/[0-9][0-9]\-[0-9][0-9]\s[0-9][0-9]:[0-9][0-9]/', array_last($messageArray['messages'])['sent_at'], $match)) ? $match[0] : '' ?></p>
                                        </div>
                                        <div class="py-[8px] px-[18px] min-w-[261px] sm:min-w-[500px] bg-greyblue rounded-[3px] border-[1px] border-[#F0F0F0] align-middle font-inter font-normal leading-[20px] tracking-normal text-[12px] text-center">
                                            <?= $messageArray['profileObject']->securePrintText($messages['content']) ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-[25px] max-w-[261px] sm:max-w-[500px] self-end">
                                        <div class="flex flex-row gap-[6px] mb-[8px]">
                                            <p class="font-inter font-normal tracking-normal leading-none text-[12px] text-grey min-w-[150px]"><?= (preg_match('/[0-9][0-9]\-[0-9][0-9]\s[0-9][0-9]:[0-9][0-9]/', array_last($messageArray['messages'])['sent_at'], $match)) ? $match[0] : '' ?></p>
                                        </div>
                                        <div class="py-[8px] px-[18px] min-w-[261px] sm:min-w-[500px] bg-white rounded-[3px] border-[1px] border-[#F0F0F0] align-middle font-inter font-normal leading-[20px] tracking-normal text-[12px] text-center">
                                            <?= $messageArray['profileObject']->securePrintText($messages['content']) ?>
                                        </div>
                                    </div>
                                <?php endif ?>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>

                <div>
                    <form id="sendMessageForm" action="/my-box" method="post" class=" flex flex-1 flex-col justify-center sm:flex-row mt-[40px] sm:mt-[114px] gap-[11px] sm:gap-[21px]">
                        <?php if (isset($toUserMember)): ?>
                            <input type="hidden" id="sendToInput" name="member" value="<?= $toUserMember->getId() ?>">
                        <?php else: ?>
                            <input type="hidden" id="sendToInput" name="member">
                        <?php endif ?>
                        <input type="text" name="content" id="message-content" class="w-[335px] sm:w-[628px] h-[49px] bg-white rounded-[6px] border-[1px] border-[#F0F0F0] placeholder:pl-[42px] pl-[42px]" placeholder="Tapez votre message ici">
                        <button type="submit" class="w-[335px] sm:w-[132px] h-[49px] font-inter font-semibold leading-none tracking-normal text-[16px] text-center text-white bg-primary duration-300 ease-in-out hover:bg-primary-hover rounded-[10px]">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    async function setRead(userId) {
        try {
            const response = await fetch('/message-read?memberId=' + userId);
            const data = await response;
        } catch (error) {
            console.error(error);
        }
    }

    document.addEventListener("DOMContentLoaded", (ev) => {
        if (window.innerWidth <= 640) {
            document.querySelector('#usersMessageDiv').classList.toggle('hidden');
            document.querySelector('#usersMessageDiv').classList.toggle('flex');
            document.querySelector('#usersDiv').classList.toggle('hidden');
            document.querySelector('#usersDiv').classList.toggle('flex');
        }

        let userId = null;
        if (document.querySelector('#sendToInput').value !== '' && document.querySelector('#sendToInput').value !== null) {
            userId = document.querySelector('#sendToInput').value;
            sessionStorage.setItem('selectedUserId', userId);
        } else {
            if (sessionStorage.getItem('selectedUserId') !== '' && sessionStorage.getItem('selectedUserId') !== null) {
                userId = sessionStorage.getItem('selectedUserId');
            } else {
                document.querySelector('#sendMessageForm').classList.add('hidden');
                document.querySelector('#sendMessageForm').classList.remove('flex');
            }
        }

        if (userId !== '' && userId !== null) {
            const messageDiv = document.querySelector("#messageContent-" + userId);

            messageDiv.classList.remove('hidden');
            messageDiv.classList.add('flex');
            messageDiv.scrollTop = messageDiv.scrollHeight;

            document.querySelector('#sendToInput').value = userId;
        }
    })

    document.querySelector('#sendMessageForm').addEventListener('submit', function(ev) {
        const userId = document.querySelector('#sendToInput').value;
        sessionStorage.setItem('selectedUserId', userId);
    });

    document.querySelector('#usersMessageDivBack').addEventListener('click', function(ev) {
        if (window.innerWidth <= 640) {
            document.querySelector('#usersMessageDiv').classList.toggle('hidden');
            document.querySelector('#usersMessageDiv').classList.toggle('flex');
            document.querySelector('#usersDiv').classList.toggle('hidden');
            document.querySelector('#usersDiv').classList.toggle('flex');
        }
    });

    document.querySelectorAll('.selectedUser').forEach(el => {
        el.addEventListener('click', function(ev) {
            document.querySelectorAll('.messageContent').forEach(
                el => {
                    el.classList.add('hidden');
                }
            )

            if (window.innerWidth <= 640) {
                document.querySelector('#usersMessageDiv').classList.toggle('hidden');
                document.querySelector('#usersMessageDiv').classList.toggle('flex');
                document.querySelector('#usersDiv').classList.toggle('hidden');
                document.querySelector('#usersDiv').classList.toggle('flex');
            }

            document.querySelectorAll('.selectedUser').forEach(el => {
                el.classList.remove('bg-white');
            })
            const selectedUser = ev.target.closest('.selectedUser')
            selectedUser.classList.add('bg-white');

            const userId = selectedUser.dataset.userId;
            sessionStorage.setItem('selectedUserId', userId);
            document.querySelector('#sendToInput').value = userId;

            const messageDiv = document.querySelector("#messageContent-" + userId);

            messageDiv.classList.remove('hidden');
            messageDiv.classList.add('flex');
            messageDiv.scrollTop = messageDiv.scrollHeight;

            setRead(userId);
            navigation.reload();
        });
    });
</script>