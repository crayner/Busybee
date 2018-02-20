<?php
namespace App\School\Form;

use App\Entity\CalendarGrade;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
	    $cal = $options['calendar_data'];
	    $builder
			->add('grade', HiddenType::class)
            ->add('students', EntityType::class,
                [
                    'class' => Student::class,
                    'label' => 'school.calendar_grade.label',
                    'help' => 'school.calendar_grade.help',
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label' => 'fullName',
                    'attr' => [
                        'class' => 'small',
                    ],
                    'query_builder' => function (EntityRepository $er) use ($cal) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.calendarGrades', 'cg')
                            ->leftJoin('cg.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id != :cal_id)')
                            ->setParameter('cal_id', $cal->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
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
				'data_class'         => CalendarGrade::class,
				'translation_domain' => 'School',
				'error_bubbling'     => true,
			]
		);
		$resolver->setRequired(
			[
                'calendar_data',
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
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['calendar_data'] = $options['calendar_data'];
	}
}
