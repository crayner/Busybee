<?php
namespace App\Timetable\Form;

use App\Entity\FaceToFace;
use App\Entity\Space;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPeriodActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activity', HiddenEntityType::class,
                [
                    'class' => FaceToFace::class,
                ]
            )
            ->add('period', HiddenEntityType::class,
                [
                    'class' => TimetablePeriod::class,
                ]
            )
            ->add('space', EntityType::class, [
                    'class' => Space::class,
                    'label' => 'activity.space.label',
                    'choice_label' => 'nameCapacity',
                    'placeholder' => 'activity.space.placeholder',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderby('s.name');
                    },
                    'help' => 'activity.space.help',
                    'help_params' => ['%{space}' => $options['data']->loadSpace()],
                    'required' => false,
                ]
            )
            ->add('tutors', CollectionType::class,
                [
                    'entry_type' => PeriodActivityTutorType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'allow_up' => true,
                    'allow_down' => true,
                    'sort_manage' => true,
                    'help' => 'activity.tutors.label',
                    'help_params' => ['%{names}' => $options['data']->loadTutorNames()],
                    'button_merge_class' => 'btn-sm',
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
                'data_class' => TimetablePeriodActivity::class,
                'translation_domain' => 'School',
                'error_bubbling' => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period_activity';
    }
}
