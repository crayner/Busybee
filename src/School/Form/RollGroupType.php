<?php
namespace App\School\Form;

use App\Core\Util\UserManager;
use App\Entity\CalendarGroup;
use App\Entity\RollGroup;
use App\Entity\Staff;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RollGroupType extends AbstractType
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * RollGroupType constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $userManager = $this->userManager;
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
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendarGroup', 'cg')
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                            ;
                    }
                ]
            )
            ->add('rollTutor2', EntityType::class, [
                    'label' => 'roll.tutor2.label',
                    'help' => 'roll.tutor2.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendarGroup', 'cg')
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
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendarGroup', 'cg')
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
            ->add('calendarGroup', EntityType::class, [
                    'label' => 'roll.calendar_group.label',
                    'help' => 'roll.calendar_group.help',
                    'placeholder' => 'roll.calendar_group.placeholder',
                    'class' => CalendarGroup::class,
                    'choice_label' => 'fullName',
                    'query_builder' => function (EntityRepository $er) use ($userManager) {
                        return $er->createQueryBuilder('cg')
                            ->leftJoin('cg.calendar', 'c')
                            ->orderBy('cg.sequence', 'ASC')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $userManager->getCurrentCalendar()->getId())
                            ;
                    },
                ]
            )
            ->add('students', EntityType::class, [
                    'label' => 'roll.students.label',
                    'help' => 'roll.calendar_group.help',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => Student::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($userManager) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rolls', 'r')
                            ->leftJoin('r.calendarGroup', 'cg')
                            ->leftJoin('cg.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $userManager->getCurrentCalendar()->getId())
                            ->andWhere()
                            ->orderBy('surname', 'ASC')
                            ->addOrderBy('firstName', 'ASC')
                            ;
                    },
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
