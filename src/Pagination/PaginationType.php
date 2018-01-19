<?php
namespace App\Pagination;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;


class PaginationType extends AbstractType
{
	/**
	 * @var Router
	 */
	private $router;

	/**
	 * PaginationType constructor.
	 *
	 * @param RouterInterface $router
	 */
	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->addLimit($builder, $options);
		$this->addHidden($builder, $options);
		$this->addSort($builder, $options);
		$this->addSearch($builder, $options);
		$this->addChoice($builder, $options);
		$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
			$event->stopPropagation();
		}, 900); // Always set a higher priority than ValidationListener

		return $builder;
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addLimit(FormBuilderInterface $builder, $options)
	{
		$choices         = array();
		$choices[10]     = '10';
		$choices[25]     = '25';
		$choices[50]     = '50';
		$choices[100]    = '100';
		$limit           = $options['data']->getLimit() < 25 ? 25 : $options['data']->getLimit();
		$choices[$limit] = $limit;
		ksort($choices, SORT_NUMERIC);
		if ($options['data']->getTotal() > 10)
			return $builder
				->add('limit', ChoiceType::class, array(
						'label'    => 'pagination.limit.label',
						'choices'  => $choices,
						'required' => true,
						'attr'     => array(
//								'disabled'				=> 'disabled',
							'onChange' => '$(this.form).submit()',
						),
						'data'     => $limit,
						'choice_translation_domain' => $options['data']->getTransDomain(),
					)
				)
				->add('lastLimit', HiddenType::class, array(
						'data' => $limit,
					)
				);
		else
			return $builder
				->add('limit', HiddenType::class, array(
						'data' => $limit,
					)
				)
				->add('lastLimit', HiddenType::class, array(
						'data' => $limit,
					)
				);
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addHidden(FormBuilderInterface $builder, $options)
	{
		$builder
			->add('offSet', HiddenType::class, array(
					'data' => $options['data']->getOffSet(),
				)
			)
			->add('total', HiddenType::class, array(
					'data' => $options['data']->getTotal(),
				)
			)
			->add('lastSearch', HiddenType::class, array(
					'data' => $options['data']->getSearch(),
				)
			);
		$builder->addEventSubscriber(new PaginationSubscriber());
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addSort(FormBuilderInterface $builder, $options)
	{
		return $builder
			->add('sortByName', ChoiceType::class, array(
					'label'                     => 'pagination.sort.label',
					'choices'                   => $options['data']->getSortList(),
					'required'                  => true,
					'attr'                      => array(
//						'disabled'				=> 'disabled',
						'class'    => 'form-inline',
						'onChange' => '$(this.form).submit()',
					),
					'data'                      => $options['data']->getSortByName(),
					'mapped'                    => false,
					'translation_domain'        => 'Pagination',
					'choice_translation_domain' => $options['data']->getTransDomain(),
				)
			);
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addSearch(FormBuilderInterface $builder, $options)
	{
		return $builder
			->add('searchData', null, array(
					'label'    => 'pagination.search.label',
					'required' => false,
					'mapped'   => false,
					'attr'     => array(
						'placeholder' => 'pagination.search.placeholder',
						'class'       => 'form-inline',
					),
				)
			);
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addChoice(FormBuilderInterface $builder, $options)
	{
		if (empty($options['data']->getChoices()))
		{
			return $builder;
		}

		$choices = [];
		foreach ($options['data']->getChoices() as $choice)
			if (isset($this->router->getRouteCollection()->all()[$choice['route']]))
				$choices[$choice['prompt']] = $this->router->getGenerator()->generate($choice['route']);

		$builder
			->add('choice', ChoiceType::class,
				[
					'required'                  => true,
					'mapped'                    => false,
					'attr'                      => [
						'class' => 'paginatorChoice'
					],
					'choices'                   => $choices,
					'label'                     => 'pagination.choice.label',
					'translation_domain'        => 'Pagination',
					'choice_translation_domain' => $options['data']->getTransDomain(),
				]
			)
			->add('lastChoice', HiddenType::class,
				[
					'mapped' => false,
				]
			);

		return $builder;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'translation_domain' => 'Pagination',
				'validation_groups'  => null,
				'attr'               => array(
					'class'       => 'ajaxForm form-inline',
					'novalidator' => 'novalidator',
				),
			)
		);
		$resolver->setRequired(
			[
				'route',
			]
		);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'paginator';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['route'] = $options['route'];
	}
}
