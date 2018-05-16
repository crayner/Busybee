<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Entity\Timetable;
use App\Entity\TimetablePeriod;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;

class PeriodPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Period';

	/**
	 * @var string
	 */
	protected $alias = 'p';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'period.time.sort' => [
			'c.sequence' => 'ASC',
			'p.start' => 'ASC',
		],
        'period.name.sort' => [
            'c.sequence' => 'ASC',
            'p.name' => 'ASC',
        ],
        'period.code.sort' => [
            'c.sequence' => 'ASC',
            'p.code' => 'ASC',
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
		'c.name',
		'p.code',
        'p.name'

	];

	/**
	 * @var array
	 */
	protected $select = [
		'p.name',
		'p.code',
		'c.name as columnName',
		'p.id',
        'p.start',
        'p.end',
	];

    /**
     * @var array
     */
	protected $join = [
	    'p.column' => [
	        'type' => 'leftJoin',
            'alias' => 'c',
        ],
        'c.timetable' => [
            'type' => 'leftJoin',
            'alias' => 't',

        ],
        't.calendar' => [
            'type' => 'leftJoin',
            'alias' => 'ca',
        ],
        'ca.calendarGrades' => [
            'type' => 'leftJoin',
            'alias' =>'cg',
        ],
    ];

	/**
	 * @var string
	 */
	protected $repositoryName = TimetablePeriod::class;

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
	 * @return    QueryBuilder
	 */
	public function buildQuery($count = false): QueryBuilder
	{
		$this->initiateQuery($count);
		if ($count)
			$this
				->setQueryJoin()
				->setSearchWhere()
                ->andCalendarGrades()
            ;
		else
			$this
				->setQuerySelect()
				->setQueryJoin()
				->setOrderBy()
				->setSearchWhere()
                ->andCalendarGrades()
            ;

		if ($this->getTimetable())
            $this->getQuery()
                ->andWhere('t = :tt_id')
                ->setParameter('tt_id', $this->getTimetable());

		return $this->getQuery();
	}

	/**
     * @var Timetable|null
     */
	private $timetable;

    /**
     * @return Timetable
     */
    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @param Timetable|null $timetable
     * @return PeriodPagination
     */
    public function setTimetable(?Timetable $timetable): PeriodPagination
    {
        $this->timetable = $timetable;
        return $this;
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
            $this->calendarGrades = CalendarManager::getCurrentCalendar()->getCalendarGrades();

        return $this->calendarGrades;
    }

    /**
     * @param Collection $calendarGrades
     * @return LinePagination
     */
    public function setCalendarGrades(Collection $calendarGrades): PeriodPagination
    {
        $this->calendarGrades = $calendarGrades;
        return $this;
    }

    /**
     * andCalendarGrades
     *
     * @return LinePagination
     */
    private function andCalendarGrades(): PeriodPagination
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
}