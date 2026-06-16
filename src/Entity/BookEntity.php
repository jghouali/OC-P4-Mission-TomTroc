<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Enum\ValidatorEnum;

class BookEntity extends AbstractEntity
{
    protected string $title;
    protected string $author;
    protected string $imagePath;
    protected string $description;
    protected BookStatusEnum $availability;
    protected MemberEntity|int $fromMember;

    public function __construct(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity|int $fromMember,
    ) {

        $this->title = $this->validateField('title', $title, ValidatorEnum::alphanumeric_150);
        $this->author = $this->validateField('author', $author, ValidatorEnum::alphanumeric_150);
        $this->imagePath = $this->validateField('imagePath', $imagePath, ValidatorEnum::uploadFile);
        $this->description = $this->validateField('description', $description, ValidatorEnum::alphanumeric_150);
        $this->availability = $availability;
        $this->fromMember = $fromMember;
    }

    public function toArray(): array
    {
        $array = [
            'title' => $this->title,
            'author' => $this->author,
            'imagePath' => $this->imagePath,
            'description' => $this->description,
            'availability' => $this->availability->value,
            'fkMemberId' => $this->fromMember->getId(),
            $this->getStorageIdName() => $this->getId(),
        ];

        return $array;
    }

    public static function getStorageIdName(): string
    {
        return 'book_id';
    }

    public function setTitle(string $title): void
    {
        $this->title = $this->validateField('title', $title, ValidatorEnum::alphanumeric_150);
    }

    public function setAuthor(string $author): void
    {
        $this->author = $this->validateField('author', $author, ValidatorEnum::alphanumeric_150);
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $this->validateField('imagePath', $imagePath, ValidatorEnum::uploadFile);
    }

    public function setDescription(string $description): void
    {
        $this->description = $this->validateField('description', $description, ValidatorEnum::alphanumeric_150);
    }

    public function setAvailability(BookStatusEnum $availability): void
    {
        $this->availability = $availability;
    }

    public function setFromMember(MemberEntity $fromMember): void
    {
        $this->fromMember = $fromMember;
    }
}
