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
 * Date: 27/05/2018
 * Time: 17:51
 */
namespace App\Core\Listener;

use App\Entity\Setting;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class SettingUpdateListener
{
    /**
     * preUpdate
     *
     * @param Setting $entity
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Setting $entity, PreUpdateEventArgs $event)
    {
        if ($entity->isValid())
            return;
        // Do not save any update at all.
        foreach ($event->getEntityChangeSet() as $name => $data)
            $event->setNewValue($name, $data[0]);
    }
}