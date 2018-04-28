<?php
namespace App\Timetable\Util;

use App\Entity\TimetableColumn;
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
     * @var TimetableColumn|null
     */
    private $column;

    /**
     * @param null|string|integer $id
     * @return TimetableColumn|null
     */
    public function find($id): ?TimetableColumn
    {
        $this->column = $this->getEntityManager()->getRepository(TimetableColumn::class)->find(intval($id));
        return $this->column;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
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