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
 * Date: 17/05/2018
 * Time: 09:40
 */

namespace App\DummyData;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;

class AppFixtures
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Bundle to manage file and directories
        $finder = new Finder();
        $finder->in(__DIR__ . '/SQL/App');
        $finder->name('*.sql');
        $finder->files();
        $finder->sortByName();

        foreach( $finder as $file ){
            echo "Importing: {$file->getBasename()} " . PHP_EOL;

            $sql = $file->getContents();

            $manager->getConnection()->exec($sql);  // Execute native SQL

            $manager->flush();
        }
    }

    /**
     * getDependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            ActivityFixtures::class,
            UserFixtures::class,
            CalendarFixtures::class,
            SchoolFixtures::class,
            TimetableFixtures::class,
            PeopleFixtures::class,
        ];
    }
}