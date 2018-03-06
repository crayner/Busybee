<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Core\Util\UserManager;
use App\Entity\ExternalActivity;
use App\School\Util\ActivityManager;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var ArrayCollection
     */
	private $data;

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
				->setSearchWhere()
            ;
		else
			$this
				->setQuerySelect()
				->setQueryJoin()
                ->setSearchWhere()
				->setOrderBy()
            ;

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

    /**
     * get Total
     *
     * @version    25th October 2016
     * @since      25th October 2016
     * @param bool $raw
     * @return    null|integer
     */
    public function getTotal($raw = false): ?int
    {
        if (empty(parent::getTotal(true)))
        {
            $query = $this->buildQuery(false)
                ->getQuery();

            $this->data = new ArrayCollection();

            foreach($query->getResult() as $result)
                if (! $this->data->contains($result))
                    $this->data->add($result);

            $this->setTotal($this->data->count());
        }

        return parent::getTotal(true);
    }

    /**
     * get Data Set
     *
     * @version    25th October 2016
     * @since      25th October 2016
     * @return    array    of Data
     */
    public function getDataSet()
    {
        $this->setPages(intval(ceil($this->getTotal() / $this->getLimit())));
        $this->result = $this->data->slice($this->getOffSet(), $this->getLimit());
        $this->writeSession();

        return $this->result;
    }

    /**
     * set Search Where
     *
     * @version    25th October 2016
     * @since      25th October 2016
     * @return    PaginationManager
     */
    public function setSearchWhere(): PaginationManager
    {
        $x = 0;
        if (!is_null($this->getSearch()))
        {
            foreach ($this->getSearchList() as $field)
            {
                $this->getQuery()->orWhere($field . ' LIKE :search' . $x);
                if (in_array($field, ['g.grade', 't.nameShort']))
                    $this->getQuery()->orWhere($field . ' IS NULL');
                $this->getQuery()->setParameter('search' . $x++, '%' . $this->getSearch() . '%');
            }
        }

        if (is_array($this->getInjectedSearch()))
            foreach ($this->getInjectedSearch() as $search)
            {
                $paramName = 'search' . $x++;
                $this->getQuery()->orWhere(str_replace('__name__', ':' . $paramName, $search['where']));
                $this->getQuery()->setParameter($paramName, $search['parameter']);
            }

        return $this;
    }
}