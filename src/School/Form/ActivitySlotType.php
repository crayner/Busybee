<?php
namespace App\School\Form;

use App\Entity\ActivitySlot;
use App\Entity\ExternalActivity;
use App\Entity\Space;
use App\School\Form\Subscriber\ActivitySlotSubscriber;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\TimeType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivitySlotType extends AbstractType
{
    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder
            ->add('startTime', TimeType::class,
                [
                    'label' => 'activity.external.slot.start.label',
                    'minutes' => [
                        0,
                        15,
                        30,
                        45,
                    ],
                ]
            )
            ->add('endTime', TimeType::class,
                [
                    'label' => 'activity.external.slot.end.label',
                    'minutes' => [
                        0,
                        15,
                        30,
                        45,
                    ],
                ]
            )
            ->add('day', ChoiceType::class,
                [
                    'label' => 'activity.external.slot.day.label',
                    'placeholder' => 'activity.external.slot.day.placeholder',
                    'choices' => [
                        'schoolweek.monday' => '1',
                        'schoolweek.tuesday' => '2',
                        'schoolweek.wednesday' => '3',
                        'schoolweek.thursday' => '4',
                        'schoolweek.friday' => '5',
                        'schoolweek.saturday' => '6',
                        'schoolweek.sunday' => '7',
                    ],
                    'choice_translation_domain' => 'Setting',
                ]
            )
            ->add('type', ToggleType::class,
                [
                    'label' => 'activity.external.slot.type.label',
                    'button_class_off' => 'btn btn-info halflings halflings-log-in',
                    'button_toggle_swap' => [
                        'btn-info',
                        'btn-primary',
                        'halflings-log-in',
                        'halflings-log-out',
                    ],
                    'label_attr' => [
                        'class' => 'typeMonitor',
                    ],
                ]
            )
            ->add('space', EntityType::class,
                [
                    'class' => Space::class,
                    'label' => 'activity.external.slot.space.label',
                    'placeholder' => 'activity.external.slot.space.placeholder',
                    'required' => false,
                    'choice_label' => 'name',
                ]
            )
            ->add('externalLocation', TextType::class,
                [
                    'label' => 'activity.external.slot.external_location.label',
                    'required' => false,
                ]
            )
            ->add('activity', HiddenEntityType::class,
                [
                    'class' => ExternalActivity::class,
                ]
            )
            ->add('id', HiddenType::class,
                [
                    'attr' => [
                        'class' => 'removeElement',
                    ],
                ]
            )
        ;
	    $builder->addEventSubscriber(new ActivitySlotSubscriber());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => ActivitySlot::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'activity_slot';
	}
}
