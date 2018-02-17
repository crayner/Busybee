<?php
namespace App\Pagination;

use App\Entity\Course;

class CoursePagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Course';

	/**
	 * @var string
	 */
	protected $alias = 'c';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'course.name.sort' => [
			'c.name' => 'ASC',
			'c.code' => 'ASC',
		],
		'course.code.sort' => [
			'c.code' => 'ASC',
			'c.name' => 'ASC',
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
		'c.name',
		'c.code',
        'd.name'

	];

	/**
	 * @var array
	 */
	protected $select = [
		'c.name',
		'c.code',
		'd.name as departmentName',
		'c.id',
	];

    /**
     * @var array
     */
	protected $join = [
	    'c.departments' => [
	        'type' => 'leftJoin',
            'alias' => 'd',
        ],
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