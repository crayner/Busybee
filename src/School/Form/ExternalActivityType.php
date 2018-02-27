<?php
namespace App\School\Form;

use App\Core\Subscriber\SequenceSubscriber;
use App\Core\Type\SettingChoiceType;
use App\Entity\Activity;
use App\Entity\Space;
use App\Entity\Student;
use App\Entity\Term;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalActivityType extends AbstractType
{
    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $grades = [];
	    $activities = [];

		$builder
            ->add('name', TextType::class,
                [
                    'label' => 'external_activity.name.label',
                    'help' => 'external_activity.name.help',
                ]
            )
            ->add('students', EntityType::class,
                [
                    'label' => 'external_activity.students.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => Student::class,
                    'choices' => $this->getStudents($grades, $activities),
                    'help' =>  'external_activity.students.help',
                    'choice_label' => 'fullName',
                ]
            )
            ->add('provider', SettingChoiceType::class,
                [
                    'label' => 'external_activity.provider.label',
                    'setting_name' => 'activity.provider.type',
                    'placeholder' => 'external_activity.provider.placeholder',
                ]
            )
            ->add('tutors', CollectionType::class,
                [
                    'entry_type' => ActivityTutorType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => [
                        'class' => 'tutorCollection'
                    ],
                ]
            )
            ->add('registration', ToggleType::class,
                [
                    'label' => 'external_activity.registration.label',
                    'help' => 'external_activity.registration.help',
                ]
            )
            ->add('active', ToggleType::class,
                [
                    'label' => 'external_activity.active.label',
                ]
            )
            ->add('terms', EntityType::class,
                [
                    'label' => 'external_activity.terms.label',
                    'help' => 'external_activity.terms.help',
                    'class' => Term::class,
                    'choice_label' => 'name',
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
                ]
            )
            ->add('maxParticipants', NumberType::class,
                [
                    'label' => 'external_activity.max_participants.label',
                ]
            )
        ;
        $builder->get('tutors')->addEventSubscriber(new SequenceSubscriber());
//		$builder->addEventSubscriber($this->activitySubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Activity::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'activity';
	}

    private function getStudents(array $grades, array $activities)
    {
        return [];
        $er = $this->studentRepository;

        $xx = $er->createQueryBuilder('s')
            ->leftJoin('s.calendarGrades', 'cg')
            ->where('cg.id IN (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->orderBy('s.surname', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        $yy = $er->createQueryBuilder('s')
            ->leftJoin('s.activities', 'a')
            ->andWhere('a.id IN (:activities)')
            ->setParameter('activities', $activities, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $xx = new ArrayCollection($xx);

        foreach($yy as $student)
            $xx->removeElement($student);

        return $xx->toArray();
    }
}
