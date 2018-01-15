<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Entity\Term;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TermController extends Controller
{
	/**
	 * @Route("calendar/term/delete/{id}/", name="term_delete")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @param                        $id
	 * @param EntityManagerInterface $entityManager
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function delete($id, EntityManagerInterface $entityManager, FlashBagManager $flashBagManager)
	{
		$term = $entityManager->getRepository(Term::class)->find($id);

		$flashBagManager->setDomain('Calendar');

		$calendar = $term->getCalendar();

		if ($term->canDelete())
		{
			$entityManager->remove($term);
			$entityManager->flush();
			$flashBagManager->add(
			'success', 'year.term.delete.success',
				[
					'%name%' => $term->getName(),
				]
			);
		}
		else
		{
			$flashBagManager->add(
			'warning', 'year.term.delete.warning',
				[
					'%name%' => $term->getName(),
				]
			);
		}

		$flashBagManager->addMessages();

		return $this->redirectToRoute('calendar_edit', ['id' => $calendar->getId(), '_fragment' => 'terms']);
	}
}