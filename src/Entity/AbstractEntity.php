<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use Green\TomTroc\Enum\ValidatorEnum;
use RuntimeException;

abstract class AbstractEntity
{
    // will serve call getUsername => return $this->username
    public function __call(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'get')) {
            // Extract property name : getUsername → username
            $property = lcfirst(substr($method, 3));

            if (property_exists($this, $property)) {
                return $this->$property;
            }

            throw new RuntimeException(
                "Property '$property' does not exist on " . static::class
            );
        }

        throw new RuntimeException(
            "method '$method' does not exist on " . static::class
        );
    }

    protected function validateField(string $propertyName, mixed $field, ValidatorEnum $validator): mixed
    {
        switch ($validator->value) {
            case '50alphanumeric_-':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9\-\_\s]{1,50}$/']]
                );
                $message = $propertyName . ' must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case '150alphanumeric_-':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 150
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9\-\_\s]{1,150}$/']]
                );
                $message = $propertyName . ' must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case 'bcryptHash':
                // $2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu
                // \__/\/ \____________________/\_____________________________/
                //  Alg Cost      Salt                        Hash
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^\$2[aby]?\$\d{1,2}\$[.\/A-Za-z0-9]{53}$/']]
                );
                $message = $propertyName . ' is not a valid bcrypt hash';
                break;

            case 'uploadFile':
                // must be in /upload/avatars/ with 1 to 50 a-zA-Z0-9 chars and .png extension
                if ($propertyName === 'imagePath') {
                    $directory = 'books';
                } elseif ($propertyName === 'avatarPath') {
                    $directory = 'avatars';
                } else {
                    $directory = $propertyName;
                }
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^\/upload\/' . $directory . '\/[a-zA-Z0-9]{1,50}\.png$/']]
                );
                $message = $propertyName . ' must be stored in /upload/' . $directory .
                    '/, contain only a-z, A-Z or 0-9, and have .png extension';
                break;

            case 'email':
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_EMAIL
                );
                $message = $propertyName . ' is not a valid email';
                break;

            case 'humanDate':
                // must be a date time before now and not before 110 years ago
                $validated = (
                    $field <= date_create('now') &&
                    $field > date_create('110 years ago')
                );
                $message = $propertyName . ' must be before now and afer 110 years ago';
                break;

            case 'intCounter':
                // must be >=0
                $validated = ($field >= 0);
                $message = $propertyName . ' must be >= 0';
                break;

            default:
                throw new RuntimeException('Unknow field passed to the validator');
        }

        if ($validated) {
            return $field;
        } else {
            $message = !isset($message) ? 'Unknown error' : $message;
            throw new RuntimeException("Invalid $propertyName : $message");
        }
    }
}
