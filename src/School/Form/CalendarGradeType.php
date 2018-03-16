<?php
namespace App\School\Form;

use App\Entity\CalendarGrade;
use App\Entity\Student;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionEntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
        $builder
            ->add('students', CollectionEntityType::class,
                [
                    'class' => Student::class,
                    'label' => 'calendar_grade.students.label',
                    'help' => 'calendar_grade.students.help',
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label' => 'fullName',
                    'attr' => [
                        'class' => 'small',
                    ],
                    'block_prefix' => 'calendar_student',
                    'query_builder' => function (EntityRepository $er) use ($grades, $cg) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.calendarGrades', 'cg')
                            ->where('(cg.id = :grade_id OR cg.id NOT IN (:exclude) OR cg.id IS NULL)')
                            ->setParameter('exclude', $grades, Connection::PARAM_STR_ARRAY)
                            ->setParameter('grade_id', $cg->getId() ?: 0)
                            ->andWhere('s.status IN (:current)')
                            ->setParameter('current', ['current', 'future'], Connection::PARAM_STR_ARRAY)
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
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
}
