<?php
namespace App\Voter;

use App\Entity\Setting;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SettingVoter extends RoleVoter
{
	/**
	 * SettingVoter constructor.
	 */
	public function __construct()
	{
		parent::__construct('ROLE_');
	}

	/**
	 * @param string|array         $attribute
	 * @param mixed          $subject
	 * @param TokenInterface $token
	 *
	 * @return bool
	 */
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		if (! $subject instanceof Setting)
			return VoterInterface::ACCESS_ABSTAIN;

		if (empty($subject->getRole()))
			return VoterInterface::ACCESS_GRANTED;

		$attributes = [];
		$attributes[] = $subject->getRole();

		return $this->vote($token, $subject, $attributes);
	}
}