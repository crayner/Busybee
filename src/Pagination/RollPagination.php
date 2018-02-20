<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Core\Util\UserManager;
use App\Entity\Roll;
use App\Entity\RollGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class RollPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Roll';

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
        'r.calendarGrades' => [
            'alias' => 'g',
            'type' => 'leftJoin',
        ],
        'r.space' => [
            'alias' => 's',
            'type' => 'leftJoin',
        ],
        'g.calendar' => [
            'alias' => 'c',
            'type' => 'leftJoin',
        ],
    ];

	/**
	 * @var string
	 */
	protected $repositoryName = Roll::class;

    /**
     * @var UserManager
     */
	private $calendarManager;

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
            ->setParameter('calendar_id', $this->calendarManager->getCurrentCalendar()->getId())
        ;

		return $this->getQuery();
	}

    /**
     * RollPagination constructor.
     * @param EntityManagerInterface $entityManager
     * @param SessionInterface $session
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FormFactoryInterface $formFactory
     * @param UserManager $calendarManager
     */
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, RouterInterface $router, RequestStack $requestStack, FormFactoryInterface $formFactory, CalendarManager $calendarManager)
    {
        $this->calendarManager = $calendarManager;
        parent::__construct($entityManager, $session, $router, $requestStack, $formFactory);
    }

    /**
     * @return CalendarManager
     */
    public function getCalendarManager(): CalendarManager
    {
        return $this->calendarManager;
    }
}