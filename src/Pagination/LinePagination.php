<?php
namespace App\Pagination;

use App\Calendar\Util\CalendarManager;
use App\Entity\Calendar;
use App\Entity\TimetableLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

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
        'a.name'
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
        'l.calendar' => [
            'type' => 'leftJoin',
            'alias' => 'c',
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
            ->setParameter('calendar', CalendarManager::getCurrentCalendar());

        return $this->getQuery();
    }
}