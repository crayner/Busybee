<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Entity\Activity;
use App\Entity\Calendar;
use App\School\Util\ClassManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class ActivityPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Activity';

	/**
	 * @var string
	 */
	protected $alias = 'a';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'activity.sort.name' => [
            'a.name' => 'ASC',
            'a.code' => 'ASC',
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
        'a.code',
        'a.website',
        's.name',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'a.name',
		'a.code',
        'a.id',
        's.name as spaceName',
        'a.website'
	];

    /**
     * @var array
     */
	protected $join = [
        'a.calendarGrades' => [
            'alias' => 'cg',
            'type' => 'leftJoin',
        ],
        'a.space' => [
            'alias' => 's',
            'type' => 'leftJoin',
        ],
    ];

    /**
     * @var array
     */
	private $activityType = [
	    'class',
        'roll',
        'external',
    ];

	/**
	 * @var string
	 */
	protected $repositoryName = Activity::class;

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
	 * @return    QueryBuilder
	 */
	public function buildQuery($count = false): QueryBuilder
	{
		$this->initiateQuery($count);
        if ($count)
			$this
				->setQueryJoin()
				->setSearchWhere()
                ->andActivityType()
                ->andCalendarGrades()
            ;
		else
			$this
				->setQuerySelect()
				->setQueryJoin()
				->setOrderBy()
				->setSearchWhere()
                ->andActivityType()
                ->andCalendarGrades()
            ;

		return $this->getQuery();
	}

    /**
     * getCurrentCalendar
     *
     * @return Calendar
     */
    public function getCurrentCalendar(): Calendar
    {
        return CalendarManager::getCurrentCalendar();
    }

    /**
     * @var Collection
     */
    private $calendarGrades;

    /**
     * @return Collection
     */
    public function getCalendarGrades(): Collection
    {
        if (empty($this->calendarGrades) || $this->calendarGrades->count() === 0)
            $this->calendarGrades = $this->getCurrentCalendar()->getCalendarGrades();

        return $this->calendarGrades;
    }

    /**
     * @param Collection $calendarGrades
     * @return ActivityPagination
     */
    public function setCalendarGrades(Collection $calendarGrades): ActivityPagination
    {
        $this->calendarGrades = $calendarGrades;
        return $this;
    }

    /**
     * andCalendarGrades
     *
     * @return ActivityPagination
     */
    private function andCalendarGrades(): ActivityPagination
    {
        $grades = [];
        foreach($this->getCalendarGrades()->getIterator() as $grade)
            $grades[] = $grade->getId();
        $this->getQuery()
            ->andWhere('cg.id in (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_INT_ARRAY)
        ;
        return $this;
    }

    /**
     * @return array
     */
    public function getActivityType(): array
    {
        return $this->activityType;
    }

    /**
     * @param array $activityType
     * @return ActivityPagination
     */
    public function setActivityType(array $activityType): ActivityPagination
    {
        $this->activityType = $activityType;
        return $this;
    }

    /**
     * andActivityType
     *
     * @return ActivityPagination
     */
    private function andActivityType(): ActivityPagination
    {
        $x = '';
        foreach($this->getActivityType() as $y=>$class)
        {
            $x .= ' OR a INSTANCE OF :entity_'.$y;
            $this->getQuery()->setParameter('entity_'.$y, $class);
        }
        $x = '(' . trim($x, ' OR ') . ')';
        $this->getQuery()->andWhere($x);
        return $this;
    }

    /**
     * @var ClassManager
     */
    private $manager;

    /**
     * ActivityPagination constructor.
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FormFactoryInterface $formFactory
     * @param ClassManager $classManager
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, RequestStack $requestStack, FormFactoryInterface $formFactory, ClassManager $classManager)
    {
        parent::__construct($entityManager, $router, $requestStack, $formFactory);
        $this->manager = $classManager;
    }

    /**
     * @return ClassManager
     */
    public function getManager(): ClassManager
    {
        return $this->manager;
    }
}