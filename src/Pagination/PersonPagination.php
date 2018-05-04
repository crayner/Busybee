<?php
namespace App\Pagination;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class PersonPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Person';

	/**
	 * @var string
	 */
	protected $alias = 'p';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'person.surname.label'   => [
			'p.surname'   => 'ASC',
			'p.firstName' => 'ASC',
		],
		'person.firstName.label' => [
			'p.firstName' => 'ASC',
			'p.surname'   => 'ASC',
		],
		'person.email.label'     => [
			'p.email'     => 'ASC',
			'p.surname'   => 'ASC',
			'p.firstName' => 'ASC',
		],
	];
	/**
	 * @var int
	 */
	protected $limit = 25;

	/**
	 * @var array
	 */
	protected $searchList = [
		'p.surname',
		'p.firstName',
		'p.email',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'p.honorific as details',
		'p.surname',
		'p.firstName',
		'p.id',
		'u.id as user_id',
	];

	/**
	 * @var string
	 */
	protected $repositoryName = Person::class;

	/**
	 * @var array
	 */
	protected $join =
		[
			'p.user' => [
				'type'  => 'leftJoin',
				'alias' => 'u',
			],
			'p.phone' => [
				'type' => 'leftJoin',
				'alias' =>'ph',
			],
		];

	/**
	 * @var array
	 */
	protected $choices = [
		'all'     => [
			'route'  => 'person_manage',
			'prompt' => 'person.pagination.all',
		],
		'student' => [
			'route'  => 'student_manage',
			'prompt' => 'person.pagination.student',
		],
		'staff'   => [
			'route'  => 'staff_manage',
			'prompt' => 'person.pagination.staff',
		],
		'contact' => [
			'route'  => 'contact_manage',
			'prompt' => 'person.pagination.contact',
		],
		'user'    => [
			'route'  => 'user_manage',
			'prompt' => 'person.pagination.user',
		],
	];

	/**
	 * @var string
	 */
	protected $transDomain = 'Person';

	/**
	 * build Query
	 *
	 * @version    28th October 2016
	 * @since      28th October 2016
	 *
	 * @param    boolean $count
	 *
	 * @return    query
	 */
	public function buildQuery($count = false)
	{
		$this->initiateQuery($count);
		if ($count)
			$this
				->setQueryJoin()
				->setSearchWhere();
		else
			$this
				->setQuerySelect()
				->setQueryJoin()
				->setOrderBy()
				->setSearchWhere();

		return $this->getQuery();
	}

	public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, RequestStack $requestStack, FormFactoryInterface $formFactory)
	{
		parent::__construct($entityManager, $router, $requestStack, $formFactory);
		$this->setDisplayChoice(true);
	}
}