<?php
namespace App\Pagination;

use App\Core\Util\UserManager;
use App\Entity\RollGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class RollGroupPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'RollGroup';

	/**
	 * @var string
	 */
	protected $alias = 'r';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'roll.sort.name' => [
			'r.name' => 'ASC',
		],
	];

	/**
	 * @var int
	 */
	protected $limit = 50;

	/**
	 * @var array
	 */
	protected $searchList = [
		'r.name',
        'r.nameShort',
        'r.website',
        's.name',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'r.name',
		'r.nameShort',
        'r.id',
        's.name as spaceName',
        'r.website'
	];

    /**
     * @var array
     */
	protected $join = [
        'r.calendar' => [
            'alias' => 'c',
            'type' => 'leftJoin',
        ],
        'r.space' => [
            'alias' => 's',
            'type' => 'leftJoin',
        ],
    ];

	/**
	 * @var string
	 */
	protected $repositoryName = RollGroup::class;

    /**
     * @var UserManager
     */
	private $userManager;

	/**
	 * @var string
	 */
	protected $transDomain = 'School';

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

        $this->getQuery()
            ->andWhere('c.id = :calendar_id')
            ->setParameter('calendar_id', $this->userManager->getCurrentCalendar()->getId())
        ;

		return $this->getQuery();
	}

    /**
     * RollGroupPagination constructor.
     * @param EntityManagerInterface $entityManager
     * @param SessionInterface $session
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FormFactoryInterface $formFactory
     * @param UserManager $userManager
     */
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, RouterInterface $router, RequestStack $requestStack, FormFactoryInterface $formFactory, UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct($entityManager, $session, $router, $requestStack, $formFactory);
    }
}