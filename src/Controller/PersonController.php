<?php
namespace App\Controller;

use App\Calendar\Util\CalendarManager;
use App\Pagination\PersonPagination;
use App\People\Form\PersonType;
use App\People\Util\PersonManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{
    /**
     * @Route("/person/all/list/", name="person_manage")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param PersonPagination $personPagination
     * @param PersonManager $personManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function index(Request $request, PersonPagination $personPagination, PersonManager $personManager)
	{
		$personPagination->injectRequest($request);

		$personPagination->getDataSet();

		if ($personPagination->getReDirect() !== false)
		    return $this->redirect($personPagination->getReDirect());
		else
            return $this->render('Person/index.html.twig',
                array(
                    'pagination' => $personPagination,
                    'manager'    => $personManager,
                )
            );
	}

    /**
     * @Route("/person/edit/{id}/", name="person_edit")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param $id
     * @param PersonManager $personManager
     * @param CalendarManager $calendarManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function edit(Request $request, $id, PersonManager $personManager, CalendarManager $calendarManager)
	{
		$person = $personManager->getPerson($id);

		$formDefinition = $personManager->getTabs();

		unset($formDefinition['person'], $formDefinition['contact'], $formDefinition['address1'], $formDefinition['address2']);

		$editOptions = array();

		$form = $this->createForm(PersonType::class, $person, [
			'deletePhoto'        => $this->generateUrl('person_photo_remove', ['id' => $id]),
			'isSystemAdmin'      => $this->isGranted('ROLE_SYSTEM_ADMIN'),
			'session'            => $this->get('session'),
			'data'               => $person,
			'data_class'         => get_class($person),
			'deletePassportScan' => $this->generateUrl('student_passport_remove', ['id' => $id]),
			'deleteIDScan'       => $this->generateUrl('student_id_remove', ['id' => $id]),
		]);

		foreach ($formDefinition as $extra)
		{
			if (isset($extra['form']) && isset($extra['name']))
			{
				$options = array();
				if (!empty($extra['options']) && is_array($extra['options']))
					$options = $extra['options'];
				$name            = $extra['data']['name'];
				$options['data'] = $this->get($name)->findOneByPerson($person->getId());
				$options['data']->setPerson($person);
				$form->add($extra['name'], $extra['form'], $options);
				$name          = $extra['name'];
				$person->$name = $options['data'];

			}
			if (isset($extra['script']))
				$editOptions['script'][] = $extra['script'];
		}

		$form->handleRequest($request);

		$validator = $this->get('validator');

		$em = $this->getDoctrine()->getManager();

		if ($form->isSubmitted() && $form->isValid())
		{
			$ok = true;

			foreach ($formDefinition as $defined)
			{
				$req = isset($defined['request']['post']) ? $defined['request']['post'] : null;
				if (!is_null($req) && isset($person->$req))
				{
					$entity = $person->$req;
					$errors = $validator->validate($entity);
					if (count($errors) > 0)
					{
						foreach ($errors as $w)
						{
							$subForm = $form->get($req);
							$field   = $w->getConstraint()->errorPath;
							if (null !== $subForm->get($field))
								$subForm->get($field)->addError(new FormError($w->getMessage(), $w->getParameters()));
						}
						$ok = false;
					}
					if ($ok)
					{
						$em->persist($person->$req);
					}
				}
			}

			if ($ok)
			{
				$em->persist($person);
				$em->flush();
				if ($id === 'Add')
					return $this->redirectToRoute('person_edit', array('id' => $person->getId()));
			}
		}

		$editOptions['id']            = $id;
		$editOptions['form']          = $form->createView();
		$editOptions['fullForm']      = $form;
		$editOptions['address1']      = $personManager->getAddressManager()->formatAddress($person->getAddress1());
		$editOptions['address2']      = $personManager->getAddressManager()->formatAddress($person->getAddress2());
		$editOptions['addressLabel1'] = $personManager->getAddressManager()->getAddressListLabel($person->getAddress1());
		$editOptions['addressLabel2'] = $personManager->getAddressManager()->getAddressListLabel($person->getAddress2());
		$editOptions['identifier']    = $person->getIdentifier();
		$editOptions['addresses']     = $personManager->getAddresses($person);
		$editOptions['phones']        = $personManager->getPhones($person);
		$editOptions['calendarManager']      = $calendarManager;
		$editOptions['personManager'] = $personManager;

		return $this->render('Person/edit.html.twig',
			$editOptions
		);
	}

	/**
	 * @param Request $request
	 * @Route("/people/import/", name="people_import")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function importAction(Request $request)
	{
		$data            = new \stdClass();
		$data->returnURL = $this->generateUrl('home');

		$form = $this->createForm(ImportType::class, $data, ['action' => $this->generateUrl('people_import_match')]);


		return $this->render('People/import.html.twig',
			array(
				'form' => $form->createView(),
			)
		);
	}

	/**
	 * @param $id
	 *
	 * @return Response
	 * @Route("/person/remove/photo/{id}/", name="person_photo_remove")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function removePhotoAction(Request $request, $id, PersonManager $personManager)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$photo = $person->getPhoto();

		$person->setPhoto(null);

		if (file_exists($photo))
			unlink($photo);

		$em->persist($person);
		$em->flush();

		return $this->editAction($request, $id);
	}
}