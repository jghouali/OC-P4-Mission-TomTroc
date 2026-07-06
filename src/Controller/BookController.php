<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\View\View;
use Green\TomTroc\Manager\BookManager;
use Green\TomTroc\Manager\MemberManager;

class BookController
{
    private BookManager $bookManager;
    private AuthentificationService $authentificationService;
    private MemberManager $memberManager;

    public function __construct(
        BookManager $bookManager,
        MemberManager $memberManager,
        AuthentificationService $authentificationService
    ) {
        $this->memberManager = $memberManager;
        $this->bookManager = $bookManager;
        $this->authentificationService = $authentificationService;
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

    public function showBookDetail(int|string $bookId, ?bool $notification = null)
    {
        if (is_string($bookId) && !ctype_digit($bookId)) {
            throw new RuntimeException('Invalid bookId', 400);
        }

        $book = $this->bookManager->getBookDetail((int) $bookId);

        if (!$book) {
            throw new RuntimeException("bookId $bookId does not exist", 400);
        }

        $availableBooksView = new View($book->getTitle());
        $data = [
            'book' => $book,
            'updateIsOk' => $notification,
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
