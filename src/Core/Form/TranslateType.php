<?php
namespace App\Core\Form;

use App\Core\Manager\TranslationManager;
use App\Core\Type\SettingChoiceType;
use App\Entity\Translate;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslateType extends AbstractType
{
    /**
     * @var TranslationManager
     */
    private $translationManager;

    /**
     * TranslateType constructor.
     * @param TranslationManager $translationManager
     */
    public function __construct(TranslationManager $translationManager)
    {
        $this->translationManager = $translationManager;
    }

    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('source', ChoiceType::class,
                [
                    'label'                     => 'translate.source.label',
                    'help'                      => 'translate.source.help',
                    'choice_translation_domain' => false,
                    'choices'                   => $this->translationManager->getChoices(),
                ]
            )
            ->add('value', TextType::class,
                [
                    'label'         => 'translate.value.label',
                    'help'          => 'translate.value.help',
                ]
            )
            ->add('locale', LocaleType::class,
                [
                    'label'         => 'translate.locale.label',
                    'help'          => 'translate.locale.help',
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
				'translation_domain' => 'System',
				'data_class'         => Translate::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'translate';
	}
}
