<?php
namespace App\Controller;

use App\Address\Util\AddressManager;
use App\Repository\AddressRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
	public function index($id = 'Add', Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$am      = $this->get('busybee_people_address.model.address_manager');
		$address = $am->find($id);

		$form = $this->createForm(AddressType::class, $address, ['manager' => $am]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($address);
			$em->flush();

			$am->addMessage('success', 'address.save.success', ['%name%' => $address->getSingleLineAddress()]);
			if ($id === 'Add')
			{
				$id = $address->getId();
				$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($am->getMessageManager());

				return $this->redirectToRoute('address_manage', array('id' => $id));
			}
		}
		elseif ($form->isSubmitted())
		{
			$am->addMessage('danger', 'address.save.failure');
		}

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($am->getMessageManager());

		return $this->render('@BusybeeAddress/Address/index.html.twig',
			[
				'id'      => $id,
				'form'    => $form->createView(),
				'manager' => $am,
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
}