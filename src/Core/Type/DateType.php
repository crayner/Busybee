<?php
namespace App\Core\Type;

use App\Core\Manager\SettingManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateType extends AbstractType
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'format' => $this->settingManager->get('date.format.widget'),
			]
		);
	}

	/**
	 * DateType constructor.
	 *
	 * @param SettingManager $settingManager
	 */
	public function __construct(SettingManager $settingManager)
	{
		$this->settingManager = $settingManager;
	}

	public function getParent()
	{
		return \Symfony\Component\Form\Extension\Core\Type\DateType::class;
	}

	public function getBlockPrefix()
	{
		return 'bee_date';
	}
}