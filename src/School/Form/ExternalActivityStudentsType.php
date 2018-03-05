<?php
namespace App\School\Form;

use App\Calendar\Util\CalendarManager;
use App\Core\Type\SettingChoiceType;
use App\Entity\ActivityStudent;
use App\Entity\ExternalActivity;
use App\Entity\Invoice;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\DateTimeType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalActivityStudentsType extends AbstractType
{
    /**
     * @var CalendarManager
     */
    private $calendarManager;

    /**
     * ExternalActivityStudentsType constructor.
     * @param CalendarManager $calendarManager
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
	    $current = $this->calendarManager->getCurrentCalendar();
	    $builder
            ->add('externalStatus', SettingChoiceType::class,
                [
                    'setting_name' => 'external.activity.status.list',
                    'label' => 'activity.student.external.status.label',
                    'placeholder' => 'activity.student.external.status.placeholder',
                    'empty_data' => 'pending',
                ]
            )
            ->add('externalActivityBackup', EntityType::class,
                [
                    'label' => 'activity.student.external.activity_backup.label',
                    'placeholder' => 'activity.student.external.activity_backup.placeholder',
                    'class' => ExternalActivity::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($current) {
                        return $er->createQueryBuilder('e')
                            ->leftJoin('e.calendarGrades', 'cg')
                            ->leftJoin('cg.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $current->getId())
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
				'data_class'         => ActivityStudent::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'external_activity_student';
	}
}
