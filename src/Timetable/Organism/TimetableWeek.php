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
 * Date: 18/05/2018
 * Time: 08:40
 */

namespace App\Timetable\Organism;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TimetableWeek
{
    /**
     * @var Collection
     */
    private $days;

    /**
     * @return Collection
     */
    public function getDays(): Collection
    {
        if (empty($this->days))
            $this->days = new ArrayCollection();

        return $this->days;
    }

    /**
     * addDay
     *
     * @param TimetableDay|null $day
     * @return TimetableWeek
     */
    public function addDay(?TimetableDay $day): TimetableWeek
    {
        if (empty($day) || empty($day->getDate()) || $this->getDays()->containsKey($day->getDate()->format('Ymd')))
            return $this;

        $this->getDays()->set($day->getDate()->format('Ymd'), $day);

        return $this;
    }

    /**
     * removeDay
     *
     * @param TimetableDay|null $day
     * @return TimetableWeek
     */
    public function removeDay(?TimetableDay $day): TimetableWeek
    {
        if (empty($day) || empty($day->getDate()) || ! $this->getDays()->containsKey($day->getDate()->format('Ymd')))
            return $this;

        $this->getDays()->remove($day->getDate()->format('Ymd'));

        return $this;
    }

    /**
     * @var integer
     */
    private $number;

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return TimetableWeek
     */
    public function setNumber(int $number): TimetableWeek
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @var string
     */
    private $title;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        if (empty($this->title))
        {

        }
        return $this->title;
    }

    /**
     * @param string $title
     * @return TimetableWeek
     */
    public function setTitle(string $title): TimetableWeek
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return TimetableWeek
     */
    public function setStart(\DateTime $start): TimetableWeek
    {
        if (empty($this->start))
            $this->start = $start;
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $finish;

    /**
     * @return \DateTime
     */
    public function getFinish(): \DateTime
    {
        return $this->finish;
    }

    /**
     * @param \DateTime $finish
     * @return TimetableWeek
     */
    public function setFinish(\DateTime $finish): TimetableWeek
    {
        $this->finish = $finish;
        return $this;
    }
}