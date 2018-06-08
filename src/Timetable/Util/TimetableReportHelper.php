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
 * Date: 7/06/2018
 * Time: 09:44
 */
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\SettingManager;
use App\Entity\CalendarGrade;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TimetableReportHelper
{
    /**
     * @var ArrayCollection
     */
    private static $grades;

    /**
     * @var RequestStack
     */
    private static $stack;

    /**
     * @var SettingManager
     */
    private static $settingManager;

    /**
     * TimetableReportHelper constructor.
     * @param RequestStack $stack
     */
    public function __construct(RequestStack $stack, SettingManager $settingManager)
    {
        self::$stack = $stack;
        self::$settingManager = $settingManager;
    }

    /**
     * gradeControl
     *
     * @return Collection
     */
    public static function gradeControl(): Collection
    {
        $grades = new ArrayCollection();

        $gradeControl = self::getSession()->get('gradeControl');

        $gradeControl = is_array($gradeControl) ? $gradeControl : [];

        foreach (CalendarManager::getCurrentCalendar()->getCalendarGrades() as $q => $w)
        {
            if (isset($gradeControl[$w->getId()]) && $gradeControl[$w->getId()]) {
                if (!$grades->contains($w))
                    $grades->add($w);
            } else
                $gradeControl[$w->getId()] = false;
        }
        self::getSession()->set('gradeControl', $gradeControl);

        return $grades;
    }

    /**
     * getSession
     *
     * @return SessionInterface
     */
    private static function getSession(): SessionInterface
    {
        return self::$stack->getCurrentRequest()->getSession();
    }

    /**
     * @return ArrayCollection
     */
    public static function getGrades(): ArrayCollection
    {
        if (! empty(self::$grades))
            return self::$grades;

        self::$grades = new ArrayCollection();

        foreach(self::gradeControl() as $grade)
            self::$grades->set($grade->getId(), $grade);

        return self::$grades;
    }

    /**
     * @var array
     */
    private static $spaceTypes;

    /**
     * @return SettingManager
     */
    public static function getSettingManager(): SettingManager
    {
        return self::$settingManager;
    }

    /**
     * getSpaceTypes
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public static function getSpaceTypes(): array
    {
        return self::getSettingManager()->get('space.type.teaching_space', []);
    }

    /**
     * gradeControl
     *
     * @return Collection
     */
    public static function getAllGrades(): Collection
    {
        $grades = new ArrayCollection();

        foreach (CalendarManager::getCurrentCalendar()->getCalendarGrades() as $q => $w)
                $grades->set($w->getId(), $w);

        return $grades;
    }

    /**
     * getGradeByID
     *
     * @param $id
     * @return CalendarGrade
     */
    public static function getGradeByID($id): ?CalendarGrade
    {
        $grade = self::getAllGrades()->get($id);

        return $grade;
    }
}