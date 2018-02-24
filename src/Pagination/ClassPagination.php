<?php
namespace App\Pagination;

use App\Entity\Course;
use App\Entity\FaceToFace;

class ClassPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'FaceToFace';

	/**
	 * @var string
	 */
	protected $alias = 'f';

	/**
	 * @var array
	 */
	protected $sortByList = [
		'facetoface.name.sort' => [
			'f.name' => 'ASC',
			'f.nameShort' => 'ASC',
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
		'f.name',
		'f.nameShort',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'f.nameShort',
        'f.id',
        'f.reportable'
	];

    /**
     * @var array
     */
	protected $join = [
    ];
	/**
	 * @var string
	 */
	protected $repositoryName = FaceToFace::class;

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

        if ($this->getCourse() instanceof Course)
            $this->getQuery()
                ->leftJoin('f.course', 'c')
                ->where('c.id = :course_id')
                ->setParameter('course_id', $this->getCourse()->getId())
            ;

		return $this->getQuery();
	}

    /**
     * @var Course
     */
	private $course;

    /**
     * @return null|Course
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     * @return ClassPagination
     */
    public function setCourse(Course $course): ClassPagination
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @param FaceToFace $entity
     * @return int
     */
    public function getParticipants(FaceToFace $entity): int
    {
        dump($entity);
        return $entity->getStudents()->count();
    }
}