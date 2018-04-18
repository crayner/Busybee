<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Entity\Calendar;
use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class LinePagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Line';

	/**
	 * @var string
	 */
	protected $alias = 'l';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'line.name.sort' => [
			'l.name' => 'ASC',
			'l.code' => 'ASC',
		],
        'line.code.sort' => [
            'l.code' => 'ASC',
            'l.name' => 'ASC',
        ],
        'line.course.sort' => [
            'c.name' => 'ASC',
            'l.name' => 'ASC',
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
		'l.name',
		'l.code',
        'c.name'

	];

	/**
	 * @var array
	 */
	protected $select = [
		'l.name',
		'l.code',
		'c.name as course',
		'l.id',
	];

    /**
     * @var array
     */
	protected $join = [
	    'l.courses' => [
	        'type' => 'leftJoin',
            'alias' => 'c',
        ],
    ];

	/**
	 * @var string
	 */
	protected $repositoryName = Line::class;

	/**
	 * @var string
	 */
	protected $transDomain = 'Timetable';

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
            ->leftJoin('l.calendar', 'y')
            ->andWhere('y.id = :calendar_id')
            ->setParameter('calendar_id', $this->calendar->getId());

		return $this->getQuery();
	}

	/**
     * @var Calendar
     */
	private $calendar;

    /**
     * LinePagination constructor.
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param FormFactoryInterface $formFactory
     * @param CalendarManager $calendarManager
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, RequestStack $requestStack, FormFactoryInterface $formFactory, CalendarManager $calendarManager)
    {
        $this->calendar = $calendarManager->getCurrentCalendar();
        parent::__construct($entityManager, $router, $requestStack, $formFactory);
    }
}