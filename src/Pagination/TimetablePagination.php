<?php
namespace App\Pagination;

use App\Entity\Course;

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
		'course.name.sort' => [
			't.name' => 'ASC',
			't.code' => 'ASC',
		],
        'course.code.sort' => [
            't.code' => 'ASC',
            't.name' => 'ASC',
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
		't.name',
		't.code',
	];

	/**
	 * @var array
	 */
	protected $select = [
		't.name',
		't.code',
		't.id',
	];

    /**
     * @var array
     */
	protected $join = [
    ];
	/**
	 * @var string
	 */
	protected $repositoryName = Course::class;

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

		return $this->getQuery();
	}
}