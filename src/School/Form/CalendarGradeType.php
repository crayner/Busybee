<?php
namespace App\School\Form;

use App\Entity\CalendarGrade;
use App\Entity\Student;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionEntityType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarGradeType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $cg = $options['data'];
	    $cal = $cg->getCalendar();
	    $grades = [];
	    foreach($cal->getCalendarGrades()->getIterator() as $grade)
	        $grades[] = strval($grade->getId());
        $key = array_search ($cg->getId(), $grades);
        unset($grades[$key]);

        $builder
            ->add('students', EntityType::class,
                [
                    'class' => Student::class,
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label'  => 'fullName',
                    'query_builder' => function (EntityRepository $er) use ($cg, $grades) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.calendarGrades', 'cg')
                            ->where('(cg.id = :grade_id OR cg.id IS NULL OR cg.id NOT IN (:grades))')
                            ->setParameter('grade_id', $cg->getId())
                            ->setParameter('grades', $grades, Connection::PARAM_INT_ARRAY)
                            ->leftJoin('cg.calendar', 'c')
                            ->andWhere('(c.id IS NULL OR c.id = :cal_id)')
                            ->setParameter('cal_id', $cg->getCalendar()->getId())
                            ->andWhere('s.status IN (:statuses)')
                            ->setParameter('statuses', ['current','future'], Connection::PARAM_STR_ARRAY)
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBY('s.firstName', 'ASC')
                        ;
                    },
                    'attr' => [
                        'class' => 'small',
                    ],
                    'label' => 'calendar_grade.students.label',
                    'help' => 'calendar_grade.students.help',
                ]
            )
            ->add('id', HiddenEntityType::class,
                [
                    'class' => CalendarGrade::class,
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
				'data_class'         => CalendarGrade::class,
				'translation_domain' => 'Calendar',
				'error_bubbling'     => true,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar_grade';
	}

    /**
     * @var EntityManagerInterface
     */
	private $em;

    /**
     * CalendarGradeType constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }
}
