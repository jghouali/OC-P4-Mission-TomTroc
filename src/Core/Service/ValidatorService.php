<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Service;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Enum\ValidatorEnum;
use RuntimeException;

class ValidatorService
{
    public static function validateField(string $propertyName, mixed $field, ValidatorEnum $validator): mixed
    {
        switch ($validator->value) {
            case 'textContent50':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                // \p{L} — all unicode letters
                // \p{N} — all numbers
                // \p{P} — all punctuation
                // \p{Z} — all separators/spaces
                // \p{S} — all symbols
                // /u — unicode mode
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[\p{L}\p{N}\p{P}\p{Z}\p{S}]{1,50}$/u']]
                );
                $message = $propertyName . ' must only contain 50 readable characters';
                break;

            case 'textContent150':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                // \p{L} — all unicode letters
                // \p{N} — all numbers
                // \p{P} — all punctuation
                // \p{Z} — all separators/spaces
                // \p{S} — all symbols
                // /u — unicode mode
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[\p{L}\p{N}\p{P}\p{Z}\p{S}]{1,150}$/u']]
                );
                $message = $propertyName . ' must only contain 150 readable characters';
                break;

            case 'textContent2000':
                // A-Z or a-z or 0-9 or _ or - or space minimum 1 maximum 50
                // \p{L} — all unicode letters
                // \p{N} — all numbers
                // \p{P} — all punctuation
                // \p{Z} — all separators/spaces
                // \p{S} — all symbols
                // /u — unicode mode
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[\p{L}\p{N}\p{P}\p{Z}\p{S}\n\r]{1,2000}$/u']]
                );
                $message = $propertyName . ' must only contain 2000 readable characters';
                break;

            case 'bcryptHash':
                // $2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu
                // \__/\/ \____________________/\_____________________________/
                //  Alg Cost    base64 Salt             base64 Hash
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^\$2[aby]?\$\d{1,2}\$[.\/A-Za-z0-9]{53}$/']]
                );
                $message = $propertyName . ' is not a valid bcrypt hash';
                break;

            case 'clearPassword':
                // Must contain at least 1 [a-z]
                // Must contain at least 1 [A-Z]
                // Must contain at least 1 [0-9]
                // Must contain at least 1 [!@#$%^&*()_\-+=.?]
                // min 12 max 72
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    [
                        'options' =>
                        [
                            'regexp' =>
                            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_\-+=.?])' .
                                '[a-zA-Z0-9!@#$%^&*()_\-+=.?]{12,72}$/',
                        ],
                    ]
                );
                $message = $propertyName .
                    ' must contain between 12 and 72 character and at least one [a-z],' .
                    ' one [0-9], one [!@#$%^&*()_\-+=.?]';
                break;

            case 'imagePath':
                // must be in /upload/avatars/ with 1 to 50 a-zA-Z0-9 chars and .png extension
                if ($propertyName === 'imagePath') {
                    $formIdName = 'profil-imagePath';
                    $uploadDir = 'books';
                } elseif ($propertyName === 'avatarPath') {
                    $formIdName = 'profil-avatarPath';
                    $uploadDir = 'avatars';
                } else {
                    $uploadDir = "$propertyName";
                }

                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    [
                        'options' =>
                        ['regexp' => '/^\/upload\/' . $uploadDir . '\/[a-zA-Z0-9\_\-\s\.&\,]{1,50}\.(png|jpg)$/'],
                    ]
                );
                $message = $propertyName . ' must be stored in /upload/' . $uploadDir .
                    '/, contain only a-z, A-Z or 0-9, and have .png extension';
                break;

            case 'uploadFile':
                // must be in /upload/avatars/ with 1 to 50 a-zA-Z0-9 chars and .png extension
                if ($propertyName === 'imagePath') {
                    $formIdName = 'profil-imagePath';
                    $uploadDir = 'books';
                } elseif ($propertyName === 'avatarPath') {
                    $formIdName = 'profil-avatarPath';
                    $uploadDir = 'avatars';
                } else {
                    $uploadDir = "$propertyName";
                }

                $result = self::uploadFile($field, $uploadDir);

                $validated = filter_var(
                    $result,
                    FILTER_VALIDATE_REGEXP,
                    [
                        'options' =>
                        ['regexp' => '/^\/upload\/' . $uploadDir . '\/[a-zA-Z0-9\-\s\.&\,]{1,50}\.(png|jpg)$/'],
                    ]
                );
                $field = $result;
                $message = $propertyName . ' must be stored in /upload/' . $uploadDir .
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
                    $field <= Locales::getLocalDateTime() &&
                    $field > Locales::getLocalDateTime('110 years ago')
                );
                $message = $propertyName . ' must be before now and afer 110 years ago';
                break;

            case 'intCounter':
                // must be >=0
                $validated = ($field >= 0);
                $message = $propertyName . ' must be >= 0';
                break;

            default:
                throw new RuntimeException('Unknow field passed to the validator', 500);
        }

        if ($validated) {
            return $field;
        } else {
            $message = !isset($message) ? 'Unknown error' : $message;
            throw new RuntimeException("Invalid $propertyName : $message", 400);
        }
    }

    public static function uploadFile(array $fileArray, string $uploadDir): string|false
    {
        $uploadDir = '/upload/' .  $uploadDir . '/';
        if (isset($fileArray) && $fileArray['error'] === 4) {
            throw new RuntimeException('No Image provided', 400);
        }
        if (isset($fileArray) && $fileArray['error'] === 0) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fileArray['tmp_name']);

            $extension = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));

            $allowedExtensions = ['png',];

            if (!in_array($extension, $allowedExtensions, true)) {
                throw new RuntimeException('Extension not allowed', 400);
            }

            if (!in_array($mimeType, ['image/png',])) {
                throw new RuntimeException('Image format not allowed', 400);
            }

            // Poids maximum : 2 Mo
            $maxFileSize = 2 * 1024 * 1024;

            if ($fileArray['size'] > $maxFileSize) {
                throw new RuntimeException('Image too big : 2Mo Max', 400);
            }

            $imageInfo = getimagesize($fileArray['tmp_name']);

            if ($imageInfo === false) {
                throw new RuntimeException('Cant read Image', 400);
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            $minWidth = 200;
            $minHeight = 200;
            $maxWidth = 2000;
            $maxHeight = 2000;

            if ($width < $minWidth || $height < $minHeight) {
                throw new RuntimeException('Image too small : 200x200 Min', 400);
            }

            if ($width > $maxWidth || $height > $maxHeight) {
                throw new RuntimeException('Image too big : 2000x2000 Max', 400);
            }

            $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = $uploadDir . $fileName;
            if (!is_dir(ROOT_DIR . '/public' . $uploadDir)) {
                mkdir(ROOT_DIR . '/public' . $uploadDir, 0755, true);
            }
            $mvFile = ROOT_DIR . '/public' . $destination;
            if (!move_uploaded_file($fileArray['tmp_name'], $mvFile)) {
                throw new RuntimeException('cant save image', 500);
            }

            return $destination;
        }
        throw new RuntimeException('There is an error while uploading file to server', 500);
    }
}
