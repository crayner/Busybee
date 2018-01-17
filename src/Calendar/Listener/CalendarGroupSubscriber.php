<?php
namespace App\Calendar\Listener;

use App\Calendar\Util\CalendarGroupManager;
use App\Core\Manager\SettingManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarGroupSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var CalendarGroupManager
	 */
	private $manager;

	/**
	 * DepartmentType constructor.
	 *
	 * @param SettingManager $om
	 */
	public function __construct(SettingManager $settingManager, CalendarGroupManager $manager)
	{
		$this->settingManager = $settingManager;
		$this->manager        = $manager;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SUBMIT   => 'preSubmit',
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();

		if ($this->manager->isStaffInstalled())
		{
			$form->add('yearTutor', EntityType::class,
				[
					'class'         => Staff::class,
					'choice_label'  => 'formatName',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')
							->orderBy('s.surname', 'ASC')
							->addOrderBy('s.firstName', 'ASC');
					},
					'placeholder'   => 'calendar.group.yeartutor.placeholder',
					'label'         => 'calendar.group.yeartutor.label',
					'required'      => false,
					'help' => 'calendar.group.yeartutor.help',
				]
			);
		}
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		if (isset($data['nameShort']))
		{
			$groups       = $this->settingManager->get('student.groups._flip');
			$data['name'] = $groups[$data['nameShort']];
		}

		$event->setData($data);
	}
}