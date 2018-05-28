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
 * Date: 28/05/2018
 * Time: 08:40
 */
namespace App\Core\Listener;

use App\Entity\Setting;
use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Psr\Container\ContainerInterface;

class SettingListenerResolver extends DefaultEntityListenerResolver
{
    /**
     * SettingListenerResolver constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * resolve
     * Returns a entity listener instance for the given class name.
     *
     * @param string $className The fully-qualified class name
     *
     * @return object An entity listener
     */
    public function resolve($className)
    {
        // resolve the service id by the given class name;
        $id = Setting::class;

        return $this->container->get($id);
    }
}