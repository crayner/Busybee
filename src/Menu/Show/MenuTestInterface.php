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
 * Date: 25/05/2018
 * Time: 10:45
 */

namespace App\Menu\Show;
/**
 * Interface MenuTestInterface
 * @package App\Menu\Show
 *
 * Required for a menu test using showTest and showOptions
 */

interface MenuTestInterface
{
    /**
     * showTest
     *
     * @param array $options
     * @return bool
     */
    public static function showTest(array $options = []): bool;
}