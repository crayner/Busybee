<?php
namespace App\Timetable\Form;

use Busybee\Core\TemplateBundle\Type\TimeType;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use App\Timetable\Entity\Column;
use App\Timetable\Entity\Period;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * TimeTableType constructor.
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
        $builder
            ->add('name', null,
                [
                    'label' => 'period.name.label',
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'period.nameShort.label',
                    'attr' =>
                        [
                            'help' => 'period.nameShort.help',
                        ],
                ]
            )
            ->add('start', TimeType::class,
                [
                    'with_seconds' => false,
                    'label' => 'period.start.label',
                ]
            )
            ->add('end', TimeType::class,
                [
                    'with_seconds' => false,
                    'label' => 'period.end.label',
                ]
            )
            ->add('break', ToggleType::class,
                [
                    'label' => 'period.break.label',
                ]
            )
            ->add('column', HiddenType::class);

        $builder->get('column')->addModelTransformer(new EntityToStringTransformer($this->om, Column::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Period::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period';
    }


}
