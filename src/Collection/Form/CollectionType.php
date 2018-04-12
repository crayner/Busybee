<?php
namespace App\Collection\Form;

use App\Collection\Form\Subscriber\CollectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    /**
     * @var CollectionSubscriber
     */
    private $collectionSubscriber;

    /**
     * CollectionType constructor.
     * @param CollectionSubscriber $collectionSubscriber
     */
    public function __construct(CollectionSubscriber $collectionSubscriber)
    {
        $this->collectionSubscriber = $collectionSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $x = clone $this->collectionSubscriber;
        $x->setOptions($options);
        $builder->addEventSubscriber($x);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'unique_key'            => 'id',
                'sort_manage'           => false,
                'allow_up'              => false,
                'allow_down'            => false,
                'route'                 => '',
                'route_params'          => [],
                'display_script'        => false,
                'add_button_class'      => '',
                'remove_button_class'   => '',
            ]
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return \Symfony\Component\Form\Extension\Core\Type\CollectionType::class;
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'hillrange_' . parent::getBlockPrefix();
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_up']             = $options['allow_up'];
        $view->vars['allow_down']           = $options['allow_down'];
        $view->vars['unique_key']           = $options['unique_key'];
        $view->vars['route']                = $options['route'];
        $view->vars['route_params']         = $options['route_params'];
        $view->vars['display_script']       = $options['display_script'];
        $view->vars['add_button_class']     = $options['add_button_class'];
        $view->vars['remove_button_class']  = $options['remove_button_class'];
    }
}