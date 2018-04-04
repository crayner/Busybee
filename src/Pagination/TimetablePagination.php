<?php
namespace App\Pagination;

use App\Entity\Timetable;

class TimetablePagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Timetable';

	/**
	 * @var string
	 */
	protected $alias = 't';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'timetable.calendar.sort' => [
			'c.firstDay' => 'ASC',
		],
        'timetable.name.sort' => [
            't.name' => 'ASC',
            't.code' => 'ASC',
        ],
        'timetable.code.sort' => [
            't.code' => 'ASC',
            't.name' => 'ASC',
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
		't.name',
		't.code',
        'c.name'

	];

	/**
	 * @var array
	 */
	protected $select = [
		't.name',
		't.code',
		'c.name as calendar',
		't.id',
        't.locked',
	];

    /**
     * @var array
     */
	protected $join = [
	    't.calendar' => [
	        'type' => 'leftJoin',
            'alias' => 'c',
        ],
    ];
	/**
	 * @var string
	 */
	protected $repositoryName = Timetable::class;

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

		return $this->getQuery();
	}
}