<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\View\View;
use Green\TomTroc\Manager\BookManager;
use Green\TomTroc\Manager\MemberManager;

class BookController
{
    private BookManager $bookManager;
    private MemberManager $memberManager;

    public function __construct(BookManager $bookManager, MemberManager $memberManager)
    {
        $this->memberManager = $memberManager;
        $this->bookManager = $bookManager;
    }

    public function showBooks(?string $search = '')
    {
        $result = $this->bookManager->listBooks($search);
        $availableBooksView = new View('Nos Livres');
        $data = [
            'avalaibleBooks' => $result,
        ];

        return $availableBooksView->render($data, TEMPLATE_DIR . '/available-books.php');
    }

    public function showBookDetail(int $bookId)
    {
        $book = $this->bookManager->getBookDetail($bookId);

        $availableBooksView = new View($book->getTitle());
        $data = [
            'book' => $book,
        ];

        return $availableBooksView->render($data, TEMPLATE_DIR . '/book-detail.php');
    }

    public function showBookEdit(int $bookId)
    {
        $book = $this->bookManager->getBookDetail($bookId);

        $availableBooksView = new View('Edit ' . $book->getTitle());
        $data = [
            'book' => $book,
        ];

        return $availableBooksView->render($data, TEMPLATE_DIR . '/book-edit.php');
    }
}
