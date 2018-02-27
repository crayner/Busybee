<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Core\Util\UserManager;
use App\Entity\ExternalActivity;
use App\School\Util\ActivityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class ExternalActivityPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'ExternalActivity';

	/**
	 * @var string
	 */
	protected $alias = 'a';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'external_activity.sort.name' => [
			'a.name' => 'ASC',
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
		'a.name',
        'a.nameShort',
        'g.grade',
        't.nameShort',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'a.name',
		'a.nameShort',
        'a.provider',
        'a.id',
        'a.payment',
	];

    /**
     * @var array
     */
	protected $join = [
        'a.calendarGrades' => [
            'alias' => 'g',
            'type' => 'leftJoin',
        ],
        'g.calendar' => [
            'alias' => 'c',
            'type' => 'leftJoin',
        ],
        'a.terms' => [
            'alias' => 't',
            'type' => 'leftJoin',
        ],
    ];

	/**
	 * @var string
	 */
	protected $repositoryName = ExternalActivity::class;

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
     * @var ActivityManager
     */
	private $activityManager;

    /**
     * RollPagination constructoa.
     * @param EntityManagerInterface $entityManager
     * @param SessionInterface $session
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FormFactoryInterface $formFactory
     * @param CalendarManager $calendarManager
     * @param ActivityManager $activityManager
     */
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, RouterInterface $router, RequestStack $requestStack,
                                FormFactoryInterface $formFactory, CalendarManager $calendarManager, ActivityManager $activityManager)
    {
        $this->calendarManager = $calendarManager;
        $this->activityManager = $activityManager;
        parent::__construct($entityManager, $session, $router, $requestStack, $formFactory);
    }

    /**
     * @return CalendarManager
     */
    public function getCalendarManager(): CalendarManager
    {
        return $this->calendarManager;
    }

    public function getTermsGrades(ExternalActivity $activity): string
    {
        return $this->activityManager->getTermsGrades($activity);
    }
}