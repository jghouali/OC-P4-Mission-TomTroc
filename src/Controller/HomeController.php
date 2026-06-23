<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\View\View;
use Green\TomTroc\Manager\BookManager;

class HomeController
{
    private BookManager $bookManager;

    public function __construct(BookManager $bookManager)
    {
        $this->bookManager = $bookManager;
    }
    public function showHomePage()
    {
        $recentBook = $this->bookManager->listLastBook(4);
        $data = [
            'recent_books' => $recentBook,
        ];
        $homeView = new View('Tomtroc Accueil');

        return $homeView->render($data, ROOT_DIR . '/templates/home.php');
    }
}
