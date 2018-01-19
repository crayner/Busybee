<?php
namespace App\People\Form\Subscriber;

use App\Core\Type\DateType;
use App\Core\Type\SettingChoiceType;
use App\Core\Type\TextType;
use App\People\Util\PersonManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PersonSubscriber implements EventSubscriberInterface
{
	/**
	 * @var PersonManager
	 */
	private $personManager;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var
	 */
	private $isSystemAdmin;

	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * PersonSubscriber constructor.
	 *
	 * @param PersonManager          $pm
	 * @param EntityManagerInterface $om
	 * @param SessionInterface       $session
	 */
	public function __construct(PersonManager $pm, EntityManagerInterface $om, SessionInterface $session)
	{
		$this->personManager = $pm;
		$this->entityManager = $om;
		$this->session       = $session;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT   => 'preSubmit',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$person = $event->getData();
		$form   = $event->getForm();

		if ($person->isStaff())
			$this->addStaffFields($form);

		if ($person->isStudent())
			$this->addStudentFields($form);

		if ($person->isUser())
		{
			$user = is_null($person->getUser()) ? new User() : $person->getUser();
			$person->setUser($user);
			$form->add('user', UserType::class, ['isSystemAdmin' => $this->isSystemAdmin, 'data' => $user, 'session' => $this->session]);
			if (empty($person->getUser()->getEmail()) || $person->getUser()->getEmail() != $person->getEmail())
				$person->getUser()->setEmail($person->getEmail());
		}

		$event->setData($person);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data   = $event->getData();
		$form   = $event->getForm();
		$person = $form->getData();
		$flush  = false;

		// Address Management
		unset($data['address1_list'], $data['address2_list']);
		if (!empty($data['address1']) || !empty($data['address2']))
		{
			if ($data['address1'] == $data['address2'])
				$data['address2'] = "";
			elseif (empty($data['address1']) && !empty($data['address2']))
			{
				$data['address1'] = $data['address2'];
				$data['address2'] = "";
			}
		}

		// Email Management
		if (!empty($data['email']) || !empty($data['email2']))
		{
			if ($data['email'] == $data['email2'])
				$data['email2'] = "";
			elseif (empty($data['email']) && !empty($data['email2']))
			{
				$data['email']  = $data['email2'];
				$data['email2'] = "";
			}
		}

		if ($person->isUser() && isset($data['user']['email']))
		{
			if (!in_array($data['user']['email'], [$data['email'], $data['email2']]))
			{
				$data['user']['email'] = $data['email'];
			}
		}

		//photo management
		if (empty($data['photo']))
		{
			$data['photo'] = $form->get('photo')->getNormData();
		}

		if ($flush)
			$this->entityManager->flush();

		if (empty($data['preferredName']))
			$data['preferredName'] = $data['firstName'];

		if ($data['photo'] instanceof File && empty($data['photo']->getFilename()))
			$data['photo'] = null;

		$event->setData($data);
	}

	/**
	 * Add Staff Fields
	 *
	 * @param $form
	 */
	private function addStaffFields($form)
	{
		$form
			->add('staffType', SettingChoiceType::class, array(
					'label'        => 'staff.stafftype.label',
					'setting_name' => 'Staff.Categories',
					'placeholder'  => 'staff.stafftype.placeholder',
					'attr'         => array(
						'class' => 'staffMember',
					)
				)
			)
			->add('jobTitle', TextType::class, array(
					'label' => 'staff.jobTitle.label',
					'attr'  => array(
						'class' => 'staffMember',
					)
				)
			)
			->add('house', SettingChoiceType::class, array(
					'label'              => 'staff.house.label',
					'placeholder'        => 'staff.house.placeholder',
					'required'           => false,
					'attr'               => array(
						'help' => 'staff.house.help',
					),
					'setting_name'       => 'house.list',
					'setting_data_name'  => 'name',
					'setting_data_value' => 'name',
				)
			)/*			->add('homeroom', EntityType::class, array(
					'label'         => 'staff.label.homeroom',
					'class'         => Space::class,
					'choice_label'  => 'name',
					'placeholder'   => 'staff.placeholder.homeroom',
					'required'      => false,
					'attr'          => array(
						'help' => 'staff.help.homeroom',
					),
					'query_builder' => function (EntityRepository $er) use ($options) {
						return $er->createQueryBuilder('h')
							->leftJoin('h.staff', 's')
							->where('s.person = :person_id')
							->orWhere('h.staff IS NULL')
							->setParameter('person_id', $options['person_id'])
							->orderBy('h.name', 'ASC');
					},
				)
			)
*/
		;

	}

	/**
	 * Add Staff Fields
	 *
	 * @param $form
	 */
	private function addStudentFields(Form $form)
	{
		$form
			->add('startAtSchool', DateType::class,
				[
					'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
					'label' => 'student.startAtSchool.label',
					'attr'  => array(
						'help'  => 'student.startAtSchool.help',
						'class' => 'student',
					),
				]
			)
			->add('startAtThisSchool', DateType::class, array(
					'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
					'label' => 'student.startAtThisSchool.label',
					'attr'  => array(
						'help'  => 'student.startAtThisSchool.help',
						'class' => 'student',
					),
				)
			)
			->add('lastAtThisSchool', DateType::class, array(
					'years'    => range(date('Y', strtotime('-5 years')), date('Y', strtotime('+18 months'))),
					'label'    => 'student.lastAtThisSchool.label',
					'attr'     => array(
						'help'  => 'student.lastAtThisSchool.help',
						'class' => 'student',
					),
					'required' => false,
				)
			)
			->add('firstLanguage', LanguageType::class, array(
					'label'       => 'student.language.first.label',
					'placeholder' => 'student.language.placeholder',
					'required'    => false,
				)
			)
			->add('secondLanguage', LanguageType::class, array(
					'label'       => 'student.language.second.label',
					'placeholder' => 'student.language.placeholder',
					'required'    => false,
				)
			)
			->add('thirdLanguage', LanguageType::class, array(
					'label'       => 'student.language.third.label',
					'placeholder' => 'student.language.placeholder',
					'required'    => false,
				)
			)
			->add('countryOfBirth', CountryType::class, array(
					'label'       => 'student.countryOfBirth.label',
					'placeholder' => 'student.countryOfBirth.placeholder',
					'required'    => false,
				)
			)
			->add('ethnicity', SettingChoiceType::class,
				array(
					'label'        => 'student.ethnicity.label',
					'placeholder'  => 'student.ethnicity.placeholder',
					'required'     => false,
					'setting_name' => 'Ethnicity.List',
				)
			)
			->add('religion', SettingChoiceType::class,
				array(
					'label'        => 'student.religion.label',
					'placeholder'  => 'student.religion.placeholder',
					'required'     => false,
					'setting_name' => 'Religion.List',
				)
			)
			->add('citizenship1', CountryType::class,
				array(
					'label'       => 'student.citizenship.1.label',
					'placeholder' => 'student.citizenship.placeholder',
					'required'    => false,
				)
			)
			->add('citizenship2', CountryType::class,
				array(
					'label'       => 'student.citizenship.2.label',
					'placeholder' => 'student.citizenship.placeholder',
					'required'    => false,
				)
			)
			->add('citizenship1Passport', TextType::class,
				array(
					'label'    => 'student.citizenship.passport.1.label',
					'required' => false,
				)
			)
			->add('citizenship2Passport', TextType::class,
				array(
					'label'    => 'student.citizenship.passport.2.label',
					'required' => false,
				)
			)
			->add('locker', TextType::class,
				array(
					'label'    => 'student.locker.label',
					'required' => false,
				)
			)
			->add('citizenship1PassportScan', ImageType::class, array(
					'attr'        => array(
						'help'       => 'student.passportScan.help',
						'imageClass' => 'headShot75',
					),
					'label'       => 'student.passportScan.label',
					'required'    => false,
					'deletePhoto' => $form->getConfig()->getOption('deletePassportScan'),
					'fileName'    => 'student_passport',
				)
			)
			->add('nationalIDCardNumber', TextType::class,
				[
					'label'    => 'student.nationalIDCardNumber.label',
					'required' => false,
				]
			)
			->add('nationalIDCardScan', ImageType::class, array(
					'attr'        => array(
						'help'       => 'student.nationalIDCardScan.help',
						'imageClass' => 'headShot75',
					),
					'label'       => 'student.nationalIDCardScan.label',
					'required'    => false,
					'deletePhoto' => $form->getConfig()->getOption('deleteIDScan'),
					'fileName'    => 'student_nationalid',
				)
			)
			->add('residencyStatus', SettingChoiceType::class,
				array(
					'label'        => 'student.residencyStatus.label',
					'placeholder'  => 'student.residencyStatus.placeholder',
					'required'     => false,
					'setting_name' => 'Residency.List',
					'attr'         => array(
						'help' => 'student.residencyStatus.help',
					),
				)
			)
			->add('visaExpiryDate', DateType::class, array(
					'years'    => range(date('Y', strtotime('-1 years')), date('Y', strtotime('+10 year'))),
					'label'    => 'student.visaExpiryDate.label',
					'attr'     => array(
						'help'  => 'student.visaExpiryDate.help',
						'class' => 'student',
					),
					'required' => false,
				)
			)
			->add('house', SettingChoiceType::class,
				[
					'label'                     => 'student.house.label',
					'placeholder'               => 'student.house.placeholder',
					'required'                  => false,
					'attr'                      => array(
						'help' => 'student.house.help',
					),
					'setting_name'              => 'house.list',
					'setting_data_name'         => 'name',
					'setting_data_value'        => 'name',
					'choice_translation_domain' => 'SystemBundle',
				]
			)
			->add('calendarGroups', CollectionType::class,
				[
					'label'         => 'student.calendar.groups.label',
					'allow_add'     => true,
					'allow_delete'  => true,
					'entry_type'    => StudentCalendarGroupType::class,
					'attr'          => [
						'class' => 'calendarGroupList',
						'help'  => 'student.calendar.groups.help',
					],
					'entry_options' => [
						'systemYear' => $form->getConfig()->getOption('systemYear'),
					],
					'constraints'   => [
						new Grades(),
					],
				]
			);
	}
}