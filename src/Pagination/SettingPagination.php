<?php
namespace App\Pagination;

use App\Entity\Setting;

class SettingPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Setting';

	/**
	 * @var string
	 */
	protected $alias = 's';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'Name' => [
			's.displayName' => 'ASC',
			's.description' => 'ASC',
		],
		'Description' => [
			's.description' => 'ASC',
			's.displayName' => 'ASC',
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
		's.displayName',
		's.description',

	];

	/**
	 * @var array
	 */
	protected $select = [
		's.displayName',
		'description',
		's.id',
	];

	/**
	 * @var string
	 */
	protected $repositoryName = Setting::class;


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