<?php
namespace App\Timetable\Form;

use App\Entity\Timetable;
use App\Timetable\Form\Subscriber\ColumnSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnType extends AbstractType
{
    /**
     * @var ColumnSubscriber
     */
    private $columnSubscriber;

    /**
     * TimeTableType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ColumnSubscriber $columnSubscriber)
    {
        $this->columnSubscriber = $columnSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Timetable::class,
                'translation_domain' => 'Timetable',
                'class' => TimeTable::class,
            ]
        );
        $resolver->setRequired(
            [
                'tt_id',
                'locked',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('columns', CollectionType::class,
                [
                    'entry_type' => ColumnPeriodType::class,
                    'entry_options' =>
                        [
                            'tt_id' => $options['tt_id'],
                        ],
                    'disabled' => $options['locked'],
                ]
            )
        ;

        $builder->addEventSubscriber($this->columnSubscriber);
    }
}
