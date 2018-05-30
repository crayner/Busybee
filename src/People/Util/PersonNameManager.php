<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 31/05/2018
 * Time: 08:12
 */

namespace App\People\Util;


use App\Core\Util\UserManager;
use App\Entity\Person;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonNameManager
{
    /**
     * @var Person
     */
    private static $person;

    /**
     * @return Person
     */
    public static function getPerson(): Person
    {
        return self::$person;
    }

    /**
     * @param Person $person
     */
    public static function setPerson(Person $person): void
    {
        self::$person = self::checkPerson($person);
    }

    /**
     * @param Person|null $person
     *
     * @return Person
     */
    private static function checkPerson(Person $person = null): Person
    {
        if ($person instanceof Person) {
            self::$person = $person;

            return self::$person;
        }

        if (self::$person instanceof Person)
            return self::$person;

        self::$person = self::getPerson();

        return self::$person;
    }

    /**
     * getFullName
     *
     * @param Person|null $person
     * @param array $options
     * @return string
     */
    public static function getFullName(Person $person = null, array $options = []): string
    {
        $person = $person ?: self::getPerson();

        if (empty($person) || ! $person instanceof Person)
            return 'No Name';

        if (empty($person->getSurname())) return '';

        $options['surnameFirst']  = !isset($options['surnameFirst']) ? true : $options['surnameFirst'];
        $options['preferredOnly'] = !isset($options['preferredOnly']) ? false : $options['preferredOnly'];

        if ($options['surnameFirst'])
        {
            if ($options['preferredOnly'])
                return $person->getSurname() . ': ' . $person->getPreferredName();

            return $person->getSurname() . ': ' . $person->getFirstName() . ' (' . $person->getPreferredName() . ')';
        }

        if ($options['preferredOnly'])
            return $person->getPreferredName() . ' ' . $person->getSurname();

        return $person->getFirstName() . ' (' . $person->getPreferredName() . ') ' . $person->getSurname();
    }

    /**
     * getFullUserName
     *
     * @param UserInterface $user
     * @return string
     */
    public static function getFullUserName(?UserInterface $user): string
    {
        if (! $user instanceof UserInterface)
            if (self::getUserManager()->getUser())
                $user = self::getUserManager()->getUser();

        if (self::getUserManager()->hasPerson($user))
            $person = self::getUserManager()->getPerson($user);

        if ($person instanceof Person)
            return self::getFullName($person);

        if ($user instanceof UserInterface)
            return $user->formatName();

        return 'No User Name' ;
    }

    /**
     * @var UserManager
     */
    private static $userManager;

    /**
     * @return UserManager
     */
    public static function getUserManager(): UserManager
    {
        return self::$userManager;
    }

    /**
     * PersonNameManager constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        self::$userManager = $userManager;
    }
}