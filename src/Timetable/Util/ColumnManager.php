<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 19/05/2018
 * Time: 08:53
 */

namespace App\Timetable\Util;


use App\Entity\TimetableColumn;
use App\Entity\TimetablePeriod;
use Doctrine\ORM\EntityManagerInterface;

class ColumnManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ColumnManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @var TimetableColumn|null
     */
    private $column;

    /**
     * find
     *
     * @param $id
     * @return TimetableColumn
     */
    public function find($id): TimetableColumn
    {
        return $this->column = $this->getEntityManager()->getRepository(TimetableColumn::class)->find($id);
    }

    /**
     * generatePeriods
     *
     */
    public function generatePeriods()
    {
        if ($this->getColumn()->getPeriods()->count() > 0)
            return;

        $periods = $this->getSettingManager()->get('schoolday.periods');

        foreach($periods as $name => $value)
        {
            $period = new TimetablePeriod();
            $period->setName($name);
            $period->setCode(isset($value['code']) ? $value['code'] : mb_substr($name, 0, 3));
            $period->setStart(new \DateTime('1970-01-01 '.$value['start']));
            $period->setEnd(new \DateTime('1970-01-01 '.$value['end']));
            $period->setPeriodType(isset($value['type']) ? $value['type'] : 'class');
            $this->getColumn()->addPeriod($period);
            $this->getEntityManager()->persist($period);
        }
        $this->getEntityManager()->persist($this->getColumn());
        $this->getEntityManager()->flush();
    }

    /**
     * @return TimetableColumn|null
     */
    public function getColumn(): ?TimetableColumn
    {
        return $this->column;
    }

    /**
     * @param TimetableColumn|null $column
     * @return ColumnManager
     */
    public function setColumn(?TimetableColumn $column): ColumnManager
    {
        $this->column = $column;
        return $this;
    }
}