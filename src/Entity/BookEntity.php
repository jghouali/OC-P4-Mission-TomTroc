<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use Green\TomTroc\Core\Service\ValidatorService;
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

        $this->title = ValidatorService::validateField(
            'title',
            $title,
            ValidatorEnum::textContent150
        );
        $this->author = ValidatorService::validateField(
            'author',
            $author,
            ValidatorEnum::textContent150
        );
        $this->imagePath = ValidatorService::validateField(
            'imagePath',
            $imagePath,
            ValidatorEnum::imagePath
        );
        $this->description = ValidatorService::validateField(
            'description',
            $description,
            ValidatorEnum::textContent2000
        );
        $this->availability = $availability;
        $this->fromMember = $fromMember;
    }

    public function toArray(): array
    {
        $array = [
            'title' => $this->title,
            'author' => $this->author,
            'image_path' => $this->imagePath,
            'description' => $this->description,
            'availability' => $this->availability->value,
            'fk_member_id' => $this->fromMember->getId(),
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
        $this->title = ValidatorService::validateField(
            'title',
            $title,
            ValidatorEnum::textContent150
        );
    }

    public function setAuthor(string $author): void
    {
        $this->author = ValidatorService::validateField(
            'author',
            $author,
            ValidatorEnum::textContent150
        );
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = ValidatorService::validateField(
            'imagePath',
            $imagePath,
            ValidatorEnum::imagePath
        );
    }

    public function setDescription(string $description): void
    {
        $this->description = ValidatorService::validateField(
            'description',
            $description,
            ValidatorEnum::textContent2000
        );
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
