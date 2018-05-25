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
 * Date: 20/05/2018
 * Time: 10:57
 */
namespace App\DummyData;

use App\Entity\Activity;
use App\Entity\ActivitySlot;
use App\Entity\ActivityStudent;
use App\Entity\ActivityTutor;
use App\Entity\CalendarGrade;
use App\Entity\ExternalActivity;
use App\Entity\Term;
use App\Entity\TimetablePeriodActivity;
use App\Entity\TimetablePeriodActivityTutor;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class ActivityStudentFixtures implements DummyDataInterface
{
    use buildTable;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager, LoggerInterface $logger)
    {
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity_student.yml'));

        $this->setLogger($logger)->buildTable($data, ActivityStudent::class, $manager);
    }
}