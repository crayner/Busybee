<?php
namespace App\Controller;

use App\Address\Form\AddressType;
use App\Address\Form\LocalityType;
use App\Address\Util\AddressManager;
use App\Address\Util\LocalityManager;
use App\Core\Manager\FlashBagManager;
use App\Entity\Address;
use App\Entity\Locality;
use App\Repository\AddressRepository;
use App\Repository\LocalityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class AddressController extends Controller
{
	/**
	 * @param string  $id
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @Route("/address/edit/{id}/", name="address_manage")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function index($id = 'Add', Request $request, AddressManager $addressManager, \Twig_Environment $twig, FlashBagManager $flashBagManager)
	{
		$address = $addressManager->find($id);

		$form = $this->createForm(AddressType::class, $address, ['manager' => $addressManager]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($address);
			$em->flush();

			$addressManager->getMessageManager()->addMessage('success', 'address.save.success', ['%name%' => $address->getSingleLineAddress()], 'Person');
			if ($id === 'Add')
			{
				$id = $address->getId();

				$flashBagManager->addMessages($addressManager->getMessageManager());

				return $this->redirectToRoute('address_manage', ['id' => $id]);
			}
		}
		elseif ($form->isSubmitted())
		{
			$addressManager->getMessageManager()->add('warning', 'address.save.failure');
		}

		return $this->render('Address/index.html.twig',
			[
				'id'        => $id,
				'form'      => $form->createView(),
				'manager'   => $addressManager,
				'twig'      => $twig,
				'fullPage'  => true,
			]
		);
	}

	/**
	 * @param AddressRepository $addressRepository
	 * @param AddressManager    $addressManager
	 *
	 * @return JsonResponse
	 * @Route("/address/list/fetch/", name="address_fetch")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function fetch(AddressRepository $addressRepository, AddressManager $addressManager)
	{

		$addresses = $addressRepository->findBy([], ['propertyName' => 'ASC', 'streetName' => 'ASC', 'streetNumber' => 'ASC']);
		$addresses = is_array($addresses) ? $addresses : array();

		$options   = [];
		$option    = ['value' => "", "label" => $this->get('translator')->trans('person.address.placeholder', array(), 'Person')];
		$options[] = $option;
		foreach ($addresses as $address)
		{
			$option    = array('value' => strval($address->getId()), "label" => $addressManager->getAddressListLabel($address));
			$options[] = $option;
		}

		return new JsonResponse(
			array(
				'options' => $options,
			),
			200
		);
	}

	/**
	 * Fetch Action
	 *
	 * @param LocalityRepository  $localityRepository
	 * @param TranslatorInterface $translator
	 *
	 * @return JsonResponse
	 * @Route("/locality/list/fetch/", name="locality_fetch")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function fetchLocality(LocalityRepository $localityRepository, TranslatorInterface $translator)
	{
		$localities = $localityRepository->findBy(array(), array('name' => 'ASC', 'postCode' => 'ASC'));
		$localities = is_array($localities) ? $localities : array();

		$options   = array();
		$option    = array('value' => "", "text" => $translator->trans('address.placeholder.locality', array(), 'Person'));
		$options[] = $option;
		foreach ($localities as $locality)
		{
			$option    = array('value' => strval($locality->getId()), "text" => $locality->getFullLocality());
			$options[] = $option;
		}

		return new JsonResponse(
			array(
				'options' => $options,
			),
			200
		);
	}
	/**
	 * @param integer|string $id
	 * @param Request        $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @Route("/locality/manage/{id}/", name="locality_manage")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function manageLocality($id, Request $request, LocalityManager $localityManager, \Twig_Environment $twig, FlashBagManager $flashBagManager)
	{
		$locality = $localityManager->find($id);

		$form = $this->createForm(LocalityType::class, $locality);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($locality);
			$em->flush();

			$localityManager->getMessageManager()->addMessage('success', 'locality.save.success', ['%name%' => $locality->getFullLocality()], 'Person');

			$flashBagManager->addMessages($localityManager->getMessageManager());

			if ($id === 'Add')
				return $this->redirectToRoute('locality_manage', array('id' => $locality->getId()));
		}

		return $this->render('Locality/index.html.twig',
			[
				'id'      => $id,
				'form'    => $form->createView(),
				'manager' => $localityManager,
				'twig'    => $twig,
				'fullPage' => true,
			]
		);
	}

	/**
	 * @param int $id
	 *
	 * @return RedirectResponse
	 * @Route("/locality/delete/{id}/", name="locality_delete")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function deleteLocality($id, LocalityManager $localityManager, FlashBagManager $flashBagManager)
	{
		$entity = $localityManager->find($id);

		if ($id > 0 && $entity instanceof Locality)
		{
			$name = $entity->getFullLocality();
			if ($localityManager->canDelete())
			{
				$em = $this->getDoctrine()->getManager();
				$em->remove($entity);
				$em->flush();

				$localityManager->getMessageManager()->addMessage('success', 'locality.delete.success', ['%name%' => $name]);
			}
			else
			{
				$localityManager->getMessageManager()->addmessage('warning', 'locality.delete.notAllowed', ['%name%' => $name]);
			}
		}

		$flashBagManager->addMessages($localityManager->getMessageManager());

		return $this->redirectToRoute('locality_manage', array('id' => 'Add'));
	}

	/**
	 * @param int $id
	 *
	 * @return RedirectResponse
	 * @Route("/address/delete/{id}/", name="address_delete")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function delete($id, AddressManager $addressManager, FlashBagManager $flashBagManager)
	{
		$entity = $addressManager->find($id);

		if ($id > 0 && $entity instanceof Address)
		{
			$name = $entity->getSingleLineAddress();
			if ($addressManager->canDelete())
			{
				$em = $this->getDoctrine()->getManager();
				$em->remove($entity);
				$em->flush();

				$addressManager->getMessageManager()->addMessage('info', 'address.delete.success', ['%name%' => $name], 'Person');
			}
			else
			{
				$addressManager->getMessageManager()->addmessage('warning', 'address.delete.notAllowed', ['%name%' => $name], 'Person');
			}
		}

		$flashBagManager->addMessages($addressManager->getMessageManager());

		return $this->redirectToRoute('address_manage', array('id' => 'Add'));
	}
}