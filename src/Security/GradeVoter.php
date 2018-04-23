<?php
namespace App\Security;

use App\People\Util\PersonManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GradeVoter extends Voter
{
	/**
	 * @var AccessDecisionManagerInterface
	 */
	private $decisionManager;

	/**
	 * @var PersonManager
	 */
	private $personManager;

	/**
	 * GradeVoter constructor.
	 *
	 * @param AccessDecisionManagerInterface $decisionManager
	 */
	public function __construct(AccessDecisionManagerInterface $decisionManager, PersonManager $personManager)
	{
		$this->decisionManager = $decisionManager;
		$this->personManager   = $personManager;
	}

	/**
	 * @param string $attribute
	 * @param mixed  $subject
	 *
	 * @return bool
	 */
	protected function supports($attribute, $subject)
	{

		if (!$subject instanceof VoterDetails)
			return false;

		return true;
	}

	/**
	 * @param string         $attribute
	 * @param mixed          $subject
	 * @param TokenInterface $token
	 *
	 * @return bool
	 */
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		if (!$this->decisionManager->decide($token, array('ROLE_TEACHER')))
			return false;

		$user = $token->getUser();

		if (!$user instanceof User)
			return false;

		if (!$this->personManager->isStaff($user->getPerson()))
			return false;

		$grades = $this->personManager->getStaffGrades($user->getPerson());

		foreach ($subject->getGrades()->toArray() as $grade)
			if ($grades->contains($grade))
				return true;

		return false;
	}
}