<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Core\Manager\SettingManager;
use App\Entity\SpecialDay;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SpecialDayController extends Controller
{
	/**
	 * @param $id
	 * @param $year
	 * @Route("/calendar/special/day/delete/{id}/", name="special_day_delete")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @return RedirectResponse
	 */
	public function deleteAction($id, EntityManagerInterface $entityManager, FlashBagManager $flashBagManager, SettingManager $settingManager)
	{
		$sday = $entityManager->getRepository(SpecialDay::class)->find($id);

		$calendar = $sday->getCalendar();
		$flashBagManager->setDomain('Calendar');

		if ($sday->canDelete())
		{
			$em = $this->get('doctrine')->getManager();
			$em->remove($sday);
			$em->flush();
			$flashBagManager->add(
			'success', 'year.specialday.delete.success',
				[
					'%name%' => $sday->getDay()->format($settingManager->get('date.format.short')),
				]
			);
		}
		else
		{
			$flashBagManager->add(
			'warning', 'year.specialday.delete.warning',
				[
					'%name%' => $sday->getDay()->format($settingManager->get('date.format.short')),
				]
			);
		}

		$flashBagManager->addMessages();
		return $this->redirectToRoute('calendar_edit', ['id' => $calendar->getId(), '_fragment' => 'specialDays']);
	}
}
