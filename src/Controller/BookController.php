<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\Http\Response;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\Service\ValidatorService;
use Green\TomTroc\Core\View\View;
use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Enum\ValidatorEnum;
use Green\TomTroc\Manager\BookManager;
use Green\TomTroc\Manager\MemberManager;
use RuntimeException;

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

    public function showBookEdit(int|string $bookId)
    {
        if (is_string($bookId) && !ctype_digit($bookId)) {
            throw new RuntimeException('Invalid bookId', 400);
        }

        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $book = $this->bookManager->getBookDetail((int) $bookId);

            if (!$book) {
                throw new RuntimeException("bookId $bookId does not exist", 400);
            }

            if ($book->getFromMember()->getId() === $loggedUser->getId()) {
                $availableBooksView = new View('Edit ' . $book->getTitle());
                $data = [
                    'book' => $book,
                ];

                return $availableBooksView->render($data, TEMPLATE_DIR . '/book-edit.php');
            }
            throw new RuntimeException('You cant edit this book', 400);
        }
        throw new RuntimeException('Not Logged', 400);
    }

    public function showBookAdd()
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $addBooksView = new View('Add book');
            $data = [
                'member' => $loggedUser,
            ];

            return $addBooksView->render($data, TEMPLATE_DIR . '/book-add.php');
        }
        throw new RuntimeException('Not Logged', 400);
    }

    public function bookAdd(
        string $availability,
        ?string $title = '',
        ?string $author = '',
        ?string $description = '',
        mixed $imagePath = []
    ) {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $error = [
                'title' => false,
                'author' => false,
                'description' => false,
                'imagePath' => false,
            ];

            if (
                !ValidatorService::validateField(
                    'title',
                    $title,
                    ValidatorEnum::textContent150,
                )
            ) {
                $error['title'] = true;
            }

            if (
                !ValidatorService::validateField(
                    'author',
                    $author,
                    ValidatorEnum::textContent150,
                )
            ) {
                $error['author'] = true;
            }

            if (
                !ValidatorService::validateField(
                    'description',
                    $description,
                    ValidatorEnum::textContent2000
                )
            ) {
                $error['description'] = true;
            }

            if (is_array($imagePath)) {
                $imagePath = ValidatorService::validateField(
                    'imagePath',
                    $imagePath,
                    ValidatorEnum::uploadFile
                );
                if (!is_string($imagePath)) {
                    $error['imagePath'] = true;
                }
            } else {
                throw new RuntimeException('Unknow image type');
            }

            if (in_array(true, $error, true)) {
                $errorArray = [];
                foreach ($error as $field => $isError) {
                    if ($isError) {
                        $errorArray[$field] = $isError;
                    }
                }

                $errorMessage = 'There is error on field : ' . implode('\', \'');
                throw new RuntimeException($errorMessage, 400);
            }

            $book = $this->bookManager->addBook(
                $title,
                $author,
                $imagePath,
                $description,
                BookStatusEnum::tryFrom($availability)
            );

            if (!$book::class === 'Green\TomTroc\Entity\BookEntity') {
                throw new RuntimeException('Cant add the book', 500);
            };

            return new Response('OK', 303, ['Location:' => '/book-detail?bookId=' . $book->getId()]);
        }
        throw new RuntimeException('Not Logged', 400);
    }

    public function bookUpdate(
        string $bookId,
        string $availability,
        ?string $title = '',
        ?string $author = '',
        ?string $description = '',
        mixed $imagePath = []
    ) {
        if (!ctype_digit($bookId)) {
            throw new RuntimeException('Invalid bookId', 400);
        }

        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $book = $this->bookManager->getBookDetail((int) $bookId);

            if (!$book) {
                throw new RuntimeException("bookId $bookId does not exist", 400);
            }

            if ($book->getFromMember()->getId() === $loggedUser->getId()) {
                $availability = BookStatusEnum::tryFrom($availability);

                if ($title === '') {
                    $title = $book->getTitle();
                }
                if ($author === '') {
                    $author = $book->getAuthor();
                }
                if ($description === '') {
                    $description = $book->getDescription();
                }
                if ($imagePath['error'] === 4) {
                    $imagePath = $book->getImagePath();
                }

                $error = [
                    'title' => false,
                    'author' => false,
                    'description' => false,
                    'imagePath' => false,
                ];

                if (
                    !ValidatorService::validateField(
                        'title',
                        $title,
                        ValidatorEnum::textContent150,
                    )
                ) {
                    $error['title'] = true;
                }

                if (
                    !ValidatorService::validateField(
                        'author',
                        $author,
                        ValidatorEnum::textContent150,
                    )
                ) {
                    $error['author'] = true;
                }

                if (
                    !ValidatorService::validateField(
                        'description',
                        $description,
                        ValidatorEnum::textContent2000
                    )
                ) {
                    $error['description'] = true;
                }

                if (
                    is_string($imagePath)
                ) {
                    if (
                        !ValidatorService::validateField(
                            'imagePath',
                            $imagePath,
                            ValidatorEnum::imagePath
                        )
                    ) {
                        $error['imagePath'] = true;
                    }
                } elseif (is_array($imagePath)) {
                    $imagePath = ValidatorService::validateField(
                        'imagePath',
                        $imagePath,
                        ValidatorEnum::uploadFile
                    );
                    if (!is_string($imagePath)) {
                        $error['imagePath'] = true;
                    }
                } else {
                    throw new RuntimeException('Unknow image type');
                }

                if (in_array(true, $error, true)) {
                    $errorArray = [];
                    foreach ($error as $field => $isError) {
                        if ($isError) {
                            $errorArray[$field] = $isError;
                        }
                    }

                    $errorMessage = 'There is error on field : ' . implode('\', \'');
                    throw new RuntimeException($errorMessage, 400);
                }

                $book->setTitle($title);
                $book->setAuthor($author);
                $book->setDescription($description);
                $book->setAvailability($availability);
                $book->setImagePath($imagePath);

                if (!$this->bookManager->updateBook($book)) {
                    return $this->showBookDetail($bookId, false);
                };

                return new Response('OK', 303, ['Location:' => '/book-detail?bookId=' . $bookId]);
            }
            throw new RuntimeException('You cant edit this book', 400);
        }
        throw new RuntimeException('Not Logged', 400);
    }

    public function bookDelete(string $bookId)
    {
        if (!ctype_digit($bookId)) {
            throw new RuntimeException('Invalid bookId', 400);
        }

        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $book = $this->bookManager->getBookDetail((int) $bookId);

            if (!$book) {
                throw new RuntimeException("bookId $bookId does not exist", 400);
            }

            if ($book->getFromMember()->getId() === $loggedUser->getId()) {
                if (!$this->bookManager->deleteBook($book)) {
                    throw new RuntimeException('an error occured while deletin book', 500);
                };

                return new Response('OK', 303, ['Location:' => '/my-profile']);
            }
            throw new RuntimeException('You cant remove this book', 400);
        }
        throw new RuntimeException('Not Logged', 400);
    }
}
