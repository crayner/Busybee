<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Entity\Roll;

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
        'r.code',
        'r.website',
        's.name',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'r.name',
		'r.code',
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
            ->andWhere('c = :calendar')
            ->setParameter('calendar', $this->getCurrentCalendar())
        ;

		return $this->getQuery();
	}

	public function getCurrentCalendar()
    {
        return CalendarManager::getCurrentCalendar();
    }
}