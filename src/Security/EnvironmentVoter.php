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
 * Date: 1/06/2018
 * Time: 11:58
 */
namespace App\Security;

use App\Menu\Show\EnvironmentIs;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EnvironmentVoter extends Voter
{
    /**
     * @var string
     */
    private $env;

    /**
     * EnvironmentVoter constructor.
     * @param string $env
     */
    public function __construct(string $env)
    {
        $this->env = $env;
    }

    /**
     * supports
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!(is_string($subject) && $attribute === 'App\Security\EnvironmentVoter'))
            return false;

        if (in_array($subject, ['prod','dev','test']))
            return true;

        return false;
    }

    /**
     * voteOnAttribute
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($this->env === $subject)
            return true;
        return false;
    }

}