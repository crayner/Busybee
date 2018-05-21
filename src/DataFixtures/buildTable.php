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

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;

trait buildTable
{
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
        $parentClass = $className;
        foreach($data as $item)
        {
            $metaData = $manager->getClassMetadata($parentClass);
            if (isset($metatData->discriminatorColumn['name']))
            {
                $discriminatorName = $metatData->discriminatorColumn['name'];
                $className = $metaData->discriminatorMap[$item[$discriminatorName]];
                $metaData = $manager->getClassMetadata($className);
            }

            $fieldNames = $metaData->fieldNames;
            $mapping = $metaData->fieldMappings;
            $assocMap = $metaData->associationMappings;
            $entity = new $className();

            foreach($item as $name=>$value)
            {
                if (isset($fieldNames[$name])) {
                    $fieldName = $fieldNames[$name];
                    $method = 'set' . ucfirst($fieldName);
                    switch ($mapping[$fieldName]['type']) {
                        case 'date':
                        case 'datetime':
                            $entity->$method(new \DateTime($value));
                            break;
                        case 'time':
                            $entity->$method(new \DateTime('1970-01-01 ' . $value));
                            break;
                        case 'array':
                            $entity->$method(unserialize($value));
                            break;
                        default:
                            $entity->$method($value);
                    }
                }
                if (isset($this->assoc[$name]))
                {
                    $fieldName = $this->assoc[$name];
                    $method = 'set' . ucfirst($fieldName);
                    if (isset($assocMap[$fieldName]))
                    {
                        $map = $assocMap[$fieldName];
                        $target = $manager->getRepository($map['targetEntity'])->find($value);
                        $entity->$method($target);
                    }
                }
            }

            $manager->persist($entity);
            $count++;
        }

        echo 'Actioned ' . $count . ' records for ' . $parentClass . PHP_EOL;
        $manager->flush();
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
        foreach($data as $item)
        {
            $metaData = $manager->getClassMetadata($parentName);

            $parent = $manager->getRepository($parentName)->find($item[$parentFieldName]);
            $child = $manager->getRepository($childName)->find($item[$childFieldName]);

            $parent->$method($child);

            $manager->persist($parent);
            $count++;
        }

        echo 'Added ' . $count . ' ' . $childName . ' to ' . $parentName . PHP_EOL;
        $manager->flush();
    }
}