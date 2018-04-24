<?php
namespace App\Pagination;

use App\Entity\Course;
use App\Entity\FaceToFace;
use App\Timetable\Util\ClassManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

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
			'f.code' => 'ASC',
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
		'f.code',
	];

	/**
	 * @var array
	 */
	protected $select = [
		'f.code',
        'f.id',
        'f.reportable'
	];

    /**
     * @var array
     */
	protected $join = [];
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
        return $entity->getStudents()->count();
    }

    /**
     * @var ClassManager
     */
    private $manager;

    /**
     * ClassPagination constructor.
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