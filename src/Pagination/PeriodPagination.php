<?php
namespace App\Pagination;

use App\Entity\Timetable;
use App\Entity\TimetablePeriod;

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
		'c.name as columnNmae',
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
            'alias' =>'g',
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

		if ($this->getTimetable())
            $this->getQuery()
                ->andWhere('t.id = :tt_id')
                ->setParameter('tt_id', $this->getTimetable()->getId());

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
     * @param Timetable $timetable
     * @return PeriodPagination
     */
    public function setTimetable(Timetable $timetable): PeriodPagination
    {
        $this->timetable = $timetable;
        return $this;
    }
}