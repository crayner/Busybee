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
 * Time: 14:52
 */
namespace App\DummyData;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Log\LoggerInterface;

trait buildTable
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @return buildTable
     */
    public function setLogger(LoggerInterface $logger): DummyDataInterface
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * buildTable
     *
     * @param array $data
     * @param string $className
     * @param ObjectManager $manager
     */
    protected function buildTable(array $data, string $className, ObjectManager $manager)
    {
        $count = 0;
        $this->getLogger()->addInfo($className . ' load started.');
        $metaData = $manager->getClassMetadata($className);
        foreach ($data as $item)
        {
            try {
                $manager->getConnection()->insert($metaData->table['name'], $item);
            } catch (ForeignKeyConstraintViolationException $e)
            {
                $this->getLogger()->addError('The table row in ' . $metaData->table['name'] .' encounted an error: '.$e->getMessage(), $item);
            }
            $count++;

            if (($count % 100) == 0)
                $this->getLogger()->addInfo('Actioned ' . $count . ' records for ' . $className . ' of a maximum ' . count($data) . ' possible.  Continuing...');
        }

        if ($count === count($data))
            $this->getLogger()->addInfo('Actioned ' . $count . ' records for ' . $className . ' of a maximum ' . count($data) . ' possible.');
        else
            $this->getLogger()->addWarning('Actioned ' . $count . ' records for ' . $className . ' of a maximum ' . count($data) . ' possible.');
        return $this;
    }

    /**
     * buildJoinTable
     *
     * @param array $data
     * @param string $parentName
     * @param string $childName
     * @param string $parentFieldName
     * @param string $childFieldName
     * @param string $method
     * @param ObjectManager $manager
     */
    protected function buildJoinTable(array $data, string $parentName, string $childName, string $parentFieldName, string $childFieldName, string $method, ObjectManager $manager)
    {
        $count = 0;
        $this->getLogger()->addInfo($parentName . ' to ' . $childName . ' load started.');
        foreach ($data as $item) {
            $metaData = $manager->getClassMetadata($parentName);

            $parent = $manager->getRepository($parentName)->find($item[$parentFieldName]);
            $child = $manager->getRepository($childName)->find($item[$childFieldName]);

            if ($parent && $child && method_exists($parent, $method)) {
                $parent->$method($child);

                $manager->persist($parent);
                $count++;
            } else {
                $r = '';
                foreach ($item as $q => $w)
                    $r .= $q . ': ' . $w . ', ';
                $this->getLogger()->addError('The link failed for some reason: ' . trim($r, ', '));
            }
        }

        $this->getLogger()->addInfo('Added ' . $count . ' ' . $childName . ' to ' . $parentName . ' of a maximum ' . count($data) . ' possible.');
        $manager->flush();
    }
}
