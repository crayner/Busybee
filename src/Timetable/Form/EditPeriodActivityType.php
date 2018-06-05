<?php

namespace App\Timetable\Form;

use App\Entity\Activity;
use App\Entity\Space;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPeriodActivityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * EditPeriodActivityType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['calendar_data'];
//        $inSpace = is_null($options['data']->getInheritedSpace()) ? 'No Inheritance' : $options['data']->getInheritedSpace()->getNameCapacity();
//        $inTutor1 = is_null($options['data']->getInheritedTutor1()) ? 'No Inheritance' : $options['data']->getInheritedTutor1()->getFullName();
//        $inTutor2 = is_null($options['data']->getInheritedTutor2()) ? 'No Inheritance' : $options['data']->getInheritedTutor2()->getFullName();
//        $inTutor3 = is_null($options['data']->getInheritedTutor3()) ? 'No Inheritance' : $options['data']->getInheritedTutor3()->getFullName();
        $builder
            ->add('activity', HiddenEntityType::class,
                [
                    'class' => Activity::class,
                ]
            )
            ->add('period', HiddenEntityType::class,
                [
                    'class' => TimetablePeriod::class,
                ]
            )
            ->add('space', EntityType::class, [
                    'class' => Space::class,
                    'choice_label' => 'nameCapacity',
                    'placeholder' => 'activity.space.placeholder',
                    'translation_domain' => 'School',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('s')
                            ->orderby('s.name');
                    },
                    'help' => 'activity.space.help',
                    'label' => 'activity.space.label',
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
                    'button_merge_class' => 'btn-sm',
                ]
            )
        ;
//        $builder->addEventSubscriber(new PeriodActivitySubscriber());
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
        $resolver->setRequired([
            'calendar_data',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period_activity';
    }
}
