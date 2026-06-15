<?php

declare(strict_types=1);

namespace Tests\Entity;

use DateTime;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MemberEntityTest extends TestCase
{
    public static string $goodUsername = 'nathalire';
    public static string $goodEmail = 'nathalie@mail.com';
    public static string $goodPasswordHash = '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu';
    public static string $goodAvatarPath = '/upload/avatars/cnsk4qcds54xvx5.png';
    public static MemberStatusEnum $goodStatus = MemberStatusEnum::VALIDATED;
    public static int $goodNotificationCount = 0;

    public static array $fieldName = [
        0 => 'username',
        1 => 'email',
        2 => 'passwordHash',
        3 => 'avatarPath',
        4 => 'createdAt',
        5 => 'updatedAt',
        6 => 'notificationCount',
        7 => 'status',
    ];

    public static function generateMemberEntityDataProvider(string $label, string $payload): array
    {
        // Get a valid Data set
        $validData = self::validDataProvider()['All fields are valid'];

        // For each dataset, one error will be injected into one field at a time
        // except for $status, because it is an Enum => count($validData) - 1
        for ($i = 0; $i < count($validData) - 1; $i++) {
            $validDataTmp[$i] = [];

            for ($j = 0; $j < count($validData); $j++) {
                // Fill in a valid value
                $validDataTmp[$i][$j] = $validData[$j];

                // Fill in a invalid value
                if ($i === $j) {
                    $validDataTmp[$i][$j] = $payload;

                    // Invalid data on DateTime cannot be a string, so put a 200 years ago DateTime
                    if (self::$fieldName[$i] === 'createdAt') {
                        $validDataTmp[$i][$j] = date_create('200 years ago');
                    }

                    // Invalid data on Datetime cannot be a string, so put a 200 years ago DateTime
                    if (self::$fieldName[$i] === 'updatedAt') {
                        $validDataTmp[$i][$j] = date_create('200 years ago');
                    }
                    // enum cannot have data different than enum values
                    if (self::$fieldName[$i] === 'status') {
                        $validDataTmp[$i][$j] = MemberStatusEnum::VALIDATED;
                    }
                    // enum cannot have data different than enum values
                    if (self::$fieldName[$i] === 'notificationCount') {
                        $validDataTmp[$i][$j] = -1;
                    }
                }
            }

            // Name the Data Set by his invalid Data
            if (self::$fieldName[$i] === 'createdAt') {
                $validDataFinal[self::$fieldName[$i] . ' is 200 years ago'] = $validDataTmp[$i];
            } elseif (self::$fieldName[$i] === 'updatedAt') {
                $validDataFinal[self::$fieldName[$i] . ' is 200 years ago'] = $validDataTmp[$i];
            } elseif (self::$fieldName[$i] === 'notificationCount') {
                $validDataFinal[self::$fieldName[$i] . ' is -1'] = $validDataTmp[$i];
            } else {
                $validDataFinal[self::$fieldName[$i] . " $label"] = $validDataTmp[$i];
            }
        };

        return (isset($validDataFinal) && count($validDataFinal) >= 1) ? $validDataFinal : self::validDataProvider();
    }

    public static function validDataProvider(): array
    {
        return [
            'All fields are valid' => [
                self::$goodUsername,
                self::$goodEmail,
                self::$goodPasswordHash,
                self::$goodAvatarPath,
                date_create('now'),
                date_create('now'),
                self::$goodNotificationCount,
                self::$goodStatus,
            ],
        ];
    }

    public static function invalidDataProvider(): array
    {
        // We inject empty, sql injection and xss invalid field on the Data Set
        $is_empty = self::generateMemberEntityDataProvider(
            'is empty',
            ''
        );
        $is_sql_inj = self::generateMemberEntityDataProvider(
            '\' OR \'1\'=\'',
            '\' OR \'1\'=\''
        );
        $is_xss = self::generateMemberEntityDataProvider(
            '<audio src/onerror=alert(1)>',
            '<audio src/onerror=alert(1)>'
        );

        return array_merge($is_empty, $is_sql_inj, $is_xss);
    }

    public static function getterProvider(): array
    {
        // We take valid Data Set
        $goodMember = self::validDataProvider();
        $key = array_key_first($goodMember);
        $value = $goodMember[$key];

        // And derivate it to n*field + the name of each getter and value to test
        $getterArray = [];
        foreach (self::$fieldName as $field) {
            $method = 'get' . substr_replace($field, strtoupper(substr($field, 0, 1)), 0, 1);
            $getterArray[$method] = array_merge([$method, $field], $value);
        }

        return $getterArray;
    }

    public static function setterProvider(): array
    {
        // We take valid Data Set
        $goodMember = self::validDataProvider();
        $key = array_key_first($goodMember);
        $value = $goodMember[$key];

        // And derivate it to n*field + the name of each setter and value to test
        $setterArray = [];
        foreach (self::$fieldName as $field) {
            $method = 'set' . substr_replace($field, strtoupper(substr($field, 0, 1)), 0, 1);
            $setterArray[$method] = array_merge([$method, $field], $value);
        }

        return $setterArray;
    }

    #[DataProvider('validDataProvider')]
    public function testCanCreateAValidNewMemberEntity(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // Given valid informations on create a new MemberEntity
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // Expected MemberEntity is valid

        // A memberEntity is a child of Green\TomTroc\Entity\MemberEntity
        $this->assertSame('Green\TomTroc\Entity\MemberEntity', get_class($member));

        // It has his username
        $this->assertSame($username, $member->getUsername());

        // It has his mail
        $this->assertSame($email, $member->getEmail());

        // It has his password hash
        $this->assertSame($passwordHash, $member->getPasswordHash());

        // It has his avatar Path
        $this->assertSame($avatarPath, $member->getAvatarPath());

        // It has his creation date, representing the date of his creation
        $this->assertSame($createdAt, $member->getCreatedAt());

        // It has his update date, representing the date of his last update
        $this->assertSame($updatedAt, $member->getUpdatedAt());

        // It has his status, 'NOT-VALIDATED' or 'VALIDATED'
        $this->assertSame($status, $member->getStatus());
    }

    #[DataProvider('invalidDataProvider')]
    public function testAnAppropriateExceptionIsThrownWhenInvalidFieldIsPassedForCreateANewMemberEntity(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);

        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetUsername(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/username must only contain character in a-z, A-Z, 0-9, _ or -/');

        $member->setUserName('Bad+Username');
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetEmail(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/email is not a valid email/');

        $member->setEmail('Bad@@.com');
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetPasswordHash(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/passwordHash is not a valid bcrypt hash/');

        $member->setPasswordHash('Bad+Password');
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetAvatarPath(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/avatarPath must be stored in \/upload\/avatars\/,' .
            ' contain only a-z, A-Z or 0-9, and have .png extension/');

        $member->setAvatarPath('/../../../passwd');
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetCreatedAt(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/createdAt must be before now and afer 110 years ago/');

        $member->setCreatedAt(new DateTime('200 year ago'));
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetUpdatedAt(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/updatedAt must be before now and afer 110 years ago/');

        $member->setUpdatedAt(new DateTime('200 year ago'));
    }

    #[DataProvider('validDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetNotificationCount(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // When a field is invalid at MemberEntity creation
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/notificationCount must be >= 0/');

        $member->setNotificationCount(-2);
    }

    #[DataProvider('getterProvider')]
    public function testGettersReturnGoodData(
        string $getter,
        string $varName,
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {

        // Given valid informaton about a Member

        // When instanciate a new MemberEntity with this informations
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        // Getter will show the same informations
        $this->assertSame($$varName, $member->$getter());
    }

    #[DataProvider('setterProvider')]
    public function testSettersSetWellData(
        string $setter,
        string $varName,
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {
        // Given a memberEntity
        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            $status
        );

        $getter = substr_replace($setter, 'g', 0, 1);

        // When a setter change a property, Getter will show the new property
        switch ($varName) {
            case 'createdAt':
                $member->$setter(date_create('yesterday'));

                $this->assertEquals(date_create('yesterday'), $member->$getter());
                break;

            case 'updatedAt':
                $member->$setter(date_create('yesterday'));

                $this->assertEquals(date_create('yesterday'), $member->$getter());
                break;

            case 'status':
                $member->$setter(MemberStatusEnum::NOTVALIDATED);

                $this->assertEquals(MemberStatusEnum::NOTVALIDATED, $member->$getter());
                break;

            case 'passwordHash':
                $member->$setter('$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.');

                $this->assertEquals('$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.', $member->$getter());
                break;

            case 'avatarPath':
                $member->$setter('/upload/avatars/newimage.png');

                $this->assertEquals('/upload/avatars/newimage.png', $member->$getter());
                break;

            case 'notificationCount':
                $member->$setter(10);

                $this->assertEquals(10, $member->$getter());
                break;

            default:
                // Validate username and email
                $member->$setter('1' . $$varName);

                $this->assertSame('1' . $$varName, $member->$getter());
        }
    }
}
