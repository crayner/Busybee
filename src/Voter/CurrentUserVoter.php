<?php
namespace App\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CurrentUserVoter extends Voter
{
	/**
	 * @param string|array         $attribute
	 * @param mixed          $subject
	 * @param TokenInterface $token
	 *
	 * @return bool
	 */
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		if (intval($subject) != $subject)
			return VoterInterface::ACCESS_ABSTAIN;

		if ($token->getUser()->getId() == $subject)
			return VoterInterface::ACCESS_GRANTED;

		return VoterInterface::ACCESS_DENIED;
	}

	/**
	 * @param string $attribute
	 * @param mixed  $subject
	 *
	 * @return bool
	 */
	protected function supports($attribute, $subject)
	{
		// if the attribute isn't one we support, return false
		if ($attribute !== 'IS_CURRENT_USER')
			return false;

		// only vote on subject as int
		if (intval($subject) != $subject)
			return false;

		return true;
	}
}