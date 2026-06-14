<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use Green\TomTroc\Enum\BookStatusEnum;
use RuntimeException;

class BookEntity
{
    private string $title;
    private string $author;
    private string $imagePath;
    private string $description;
    private BookStatusEnum $availability;
    private MemberEntity $fromMember;

    public function __construct(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {

        $this->title = $this->validateField('title', $title);
        $this->author = $this->validateField('author', $author);
        $this->imagePath = $this->validateField('imagePath', $imagePath);
        $this->description = $this->validateField('description', $description);
        $this->availability = $this->validateField('availability', $availability);
        $this->fromMember = $this->validateField('fromMember', $fromMember);
    }

    private function validateField(string $property, mixed $field): mixed
    {
        switch ($property) {
            case 'title':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9\-\_\s]{1,50}$/']]
                );
                $message = 'Title must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case 'author':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9\-\_\s]{1,50}$/']]
                );
                $message = 'Author must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case 'imagePath':
                // must be in /upload/avatars/ with 1 to 50 a-zA-Z0-9 chars and .png extension
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^\/upload\/books\/[a-zA-Z0-9]{1,50}\.png$/']]
                );
                $message = 'Book image path must be stored in /upload/books/,' .
                    ' contain only a-z, A-Z or 0-9, and have .png extension';
                break;

            case 'description':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9\-\_\s]{1,50}$/']]
                );
                $message = 'Description must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case 'availability':
                // it is an Enum
                $validated = true;
                break;

            case 'fromMember':
                // must be an instance of MemberEntity
                $validated = (get_class($field) === 'Green\TomTroc\Entity\MemberEntity');
                $message = 'fromMember must be a MemberEntity';
                break;

            default:
                throw new RuntimeException('Unknow field passed to the validator');
        }

        if ($validated) {
            return $field;
        } else {
            $message = !isset($message) ? 'Unknown error' : $message;
            throw new RuntimeException("Invalid $property : $message");
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAvailability(): BookStatusEnum
    {
        return $this->availability;
    }

    public function getFromMember(): MemberEntity
    {
        return $this->fromMember;
    }

    public function setTitle(string $title): void
    {
        $this->title = $this->validateField('title', $title);
    }

    public function setAuthor(string $author): void
    {
        $this->author = $this->validateField('author', $author);
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $this->validateField('imagePath', $imagePath);
    }

    public function setDescription(string $description): void
    {
        $this->description = $this->validateField('description', $description);
    }

    public function setAvailability(BookStatusEnum $availability): void
    {
        $this->availability = $this->validateField('availability', $availability);
    }

    public function setFromMember(MemberEntity $fromMember): void
    {
        $this->fromMember = $this->validateField('fromMember', $fromMember);
    }
}
