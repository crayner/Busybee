<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Entity\TimetableLine;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;

class LinePagination extends PaginationManager
{
    /**
     * @var string
     */
    protected $paginationName = 'TimetableLine';

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
        'a.name',
    ];

    /**
     * @var array
     */
    protected $select = [
        'l.name',
        'l.code',
        'l.id',
    ];

    /**
     * @var array
     */
    protected $join = [
        'l.activities' => [
            'type' => 'leftJoin',
            'alias' => 'a',
        ],
        'a.calendarGrades' => [
            'type' => 'leftJoin',
            'alias' => 'cg',
        ],
    ];

    /**
     * @var string
     */
    protected $repositoryName = TimetableLine::class;

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

        return $this->getQuery();
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
    public function setCalendarGrades(Collection $calendarGrades): LinePagination
    {
        $this->calendarGrades = $calendarGrades;
        return $this;
    }

    /**
     * andCalendarGrades
     *
     * @return LinePagination
     */
    private function andCalendarGrades(): LinePagination
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