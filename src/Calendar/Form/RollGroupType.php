<?php
namespace App\Calendar\Form;

use App\Entity\RollGroup;
use Hillrange\Form\Type\HiddenEntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Calendar;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RollGroupType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder
			->add('nameShort', TextType::class,
				[
					'label'        => 'roll_group.nameshort.label',
					'required'     => true,
				]
			)
			->add('name', TextType::class,
                [
                    'label' => 'roll_group.name.label',
                    'required' => true,
                ]
            )
			->add('calendar', HiddenEntityType::class,
				[
					'class' => Calendar::class,
				]
			)
            ->add('grade', SettingChoiceType::class,
                [
                    'setting_name' => 'student.groups',
                    'label' => 'roll_group.grade.label',
                    'placeholder' => 'roll_group.grade.placeholder',
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
				'translation_domain' => 'Calendar',
				'calendar_data'          => null,
				'error_bubbling'     => true,
			]
		);
		$resolver->setRequired(
			[
				'manager',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'roll_group';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['calendar_data'] = $options['calendar_data'];
		$view->vars['manager']   = $options['manager'];
	}
}
