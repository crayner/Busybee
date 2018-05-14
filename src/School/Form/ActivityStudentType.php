<?php
namespace App\School\Form;

use App\Entity\Activity;
use App\Entity\ActivityStudent;
use App\Entity\Student;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityStudentType extends AbstractType
{
    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('classReportable', ToggleType::class,
                [
                    'label' => 'activity.student.reportable.label',
                    'help' => 'activity.student.reportable.help',
                    'button_merge_class' => 'btn-sm',
                ]
            )
            ->add('activity', HiddenEntityType::class,
                [
                    'class' => Activity::class,
                ]
            )
            ->add('student', EntityType::class,
                [
                    'class' => Student::class,
                    'choice_label' => 'fullName',
                    'choice_value' => 'id',
                    'label' => 'activity.student.name.label',
                    'help' => 'activity.student.name.help',
                    'placeholder' => 'activity.student.name.placeholder',
                    'choices' => $options['student_list'],
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
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
				'data_class'         => ActivityStudent::class,
				'translation_domain' => 'School',
			]
		);
		$resolver->setRequired(
		    [
		        'student_list',
            ]
        );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'class_student';
	}


}
