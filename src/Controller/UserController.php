<?php
namespace App\Controller;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\People\Util\PersonManager;
use Hillrange\Security\Entity\User;
use Hillrange\Security\Util\ParameterInjector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
	/**
	 * @param $id
	 * @Route("/person/user/toggle/{id}/", name="person_toggle_user")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function toggle($id, PersonManager $personManager, MessageManager $messageManager, ParameterInjector $parameterInjector)
	{
		$person = $personManager->find($id);

		$user = $person->getUser();

		$om = $this->getDoctrine()->getManager();

		// Remove User from person

		if ($user instanceof User && $personManager->canDeleteUser())
		{
			$om->remove($user);
			$person->setUser(null);
			$om->persist($person);
			$om->flush();

			return new JsonResponse(
				array(
					'message' => $messageManager->add('success', 'user.toggle.removeSuccess', ['%name%' => $user->formatName()], 'Person')->renderView($this->container->get('twig')),
					'status'  => 'removed',
				),
				200
			);
		}

		if (!empty($person->getEmail()) && $personManager->canBeUser())
		{
			$user = $om->getRepository(User::class)->findOneByEmail($person->getEmail());

			if (is_null($user) && !empty($person->getEmail2()))
				$user = $om->getRepository(User::class)->findOneByEmail($person->getEmail2());

			if (is_null($user))
			{
				$user = new User($parameterInjector);
				$user->setEmail($person->getEmail());
				$user->setEmailCanonical($person->getEmail());
				$user->setUsername($person->getEmail());
				$user->setUsernameCanonical($person->getEmail());
				$user->setLocale($this->getParameter('locale'));
				$user->setPassword(password_hash(uniqid(), PASSWORD_BCRYPT));
				$user->setCredentialsExpired(true);
			}
			$user->setEnabled(true);
			$user->setExpired(false);
			$user->setCredentialsExpireAt(null);
			$user->setExpiresAt(null);

			$person->setUser($user);

			$om->persist($user);
			$om->persist($person);
			$om->flush();

			return new JsonResponse(
				array(
					'message' => $messageManager->add('success', 'user.toggle.addSuccess', ['%name%' => $user->formatName()], 'Person')->renderView($this->container->get('twig')),
					'status'  => 'added',
				),
				200
			);

		}

		return new JsonResponse(
			array(
				'message' => $messageManager->add('warning', 'user.toggle.notUser', ['%name%' => $user->formatName()], 'Person')->renderView($this->container->get('twig')),
				'status'  => 'failed',
			),
			200
		);
	}
	/**
	 * @param $id
	 * @Route("/security/user/edit/{id}/", name="user_edit")
	 */
	public function userEdit($id)
	{
		return $this->redirectToRoute('person_edit', ['id' => $id, '_fragment' => 'user']);
	}

	/**
	 * @param                 $id
	 * @param                 $calendar
	 * @param CalendarManager $calendarManager
	 * @Route("/security/user/calendar/change/{id}/{calendar}/", name="user_calendar_change")
	 * @Security("is_granted('IS_CURRENT_USER', id)")
	 */
	public function userCalendar($id, $calendar)
	{
		$em = $this->getDoctrine()->getManager();

		$user = $em->getRepository(User::class)->find($id);

		$settings = $user->getUserSettings();

		if (isset($settings['calendar']) && $calendar == 'current')
			unset($settings['calendar']);

		if ($calendar > 0)
			$settings['calendar'] = intval($calendar);

		$user->setUserSettings($settings);

		$em->persist($user);
		$em->flush();

		return $this->redirectToRoute('person_edit', ['id' => $id, '_fragment' => 'user']);
	}
}