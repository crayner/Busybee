<?php
namespace App\School\Form;

use App\Calendar\Util\CalendarManager;
use App\Core\Type\SettingChoiceType;
use App\Core\Util\UserManager;
use App\Entity\Calendar;
use App\Entity\CalendarGroup;
use App\Entity\RollGroup;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RollGroupType extends AbstractType
{

    /**
     * @var CalendarManager 
     */
    private $calendarManager;

    /**
     * RollGroupType constructor.
     * @param UserManager $calendarManager
     */
    public function __construct(CalendarManager $calendarManager)
    {
        $this->calendarManager = $calendarManager;
    }

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $data = $options['data'];
	    $calendarManager = $this->calendarManager;
		$builder
            ->add('name', TextType::class, [
                    'label' => 'roll.name.label',
                    'help' => 'roll.name.help',
                ]
            )
            ->add('nameShort', TextType::class, [
                    'label' => 'roll.name_short.label',
                    'help' => 'roll.name_short.help',
                ]
            )
            ->add('rollTutor1', EntityType::class, [
                    'label' => 'roll.tutor1.label',
                    'help' => 'roll.tutor1.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($calendarManager, $data) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id = :calendar_id)')
                            ->setParameter('calendar_id', $calendarManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                ]
            )
            ->add('rollTutor2', EntityType::class, [
                    'label' => 'roll.tutor2.label',
                    'help' => 'roll.tutor2.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($calendarManager, $data) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id = :calendar_id)')
                            ->setParameter('calendar_id', $calendarManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                    'required' => false,
                ]
            )
            ->add('rollTutor3', EntityType::class, [
                    'label' => 'roll.tutor3.label',
                    'help' => 'roll.tutor3.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($calendarManager, $data) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id = :calendar_id)')
                            ->setParameter('calendar_id', $calendarManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                    'required' => false,
                ]
            )
            ->add('website', UrlType::class, [
                    'label' => 'roll.website.label',
                    'required' => false,
                ]
            )
            ->add('calendar', HiddenEntityType::class, [
                    'class' => Calendar::class,
                ]
            )
            ->add('space', EntityType::class,
                [
                    'class' => Space::class,
                    'label' => 'roll.space.label',
                    'placeholder' => 'roll.space.placeholder',
                    'choice_label' => 'name',
                ]
            )
            ->add('grade', SettingChoiceType::class,
                [
                    'setting_name' => 'student.groups',
                    'label' => 'roll_group.grade.label',
                    'placeholder' => 'roll_group.grade.placeholder',
                    'translation_domain' => 'Calendar',
                ]
            )
            ->add('students', EntityType::class, [
                    'label' => 'roll.students.label',
                    'help' => 'roll.students.help',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => Student::class,
                    'choice_label' => 'formatName',
                    'attr' => [
                        'class' => 'small',
                    ],
                    'query_builder' => function (EntityRepository $er) use ($data, $calendarManager) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $calendarManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                            ;
                    },
                ]
            )
            ->add('nextRoll', EntityType::class, 
                [
                    'label' => 'roll.next_roll.label',
                    'help' => 'roll.next_roll.help',
                    'placeholder' => 'roll.next_roll.placeholder',
                    'class' => RollGroup::class,
                    'choice_label' => 'fullName',
                    'query_builder' => function (EntityRepository $er) use ($calendarManager) {
                        return $er->createQueryBuilder('r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $calendarManager->getNextCalendar() ? $calendarManager->getNextCalendar()->getId() : null)
                            ->orderBy('r.name')
                            ;
                    },
                    'required' => false,
                ]
            )
        ;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => RollGroup::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'roll';
	}
}
