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
 * Time: 10:44
 */
namespace App\Menu\Show;

use App\Kernel;

class EnvironmentIs implements MenuTestInterface
{
    /**
     * @var string
     */
    private static $env;

    /**
     * constructor.
     * @param Kernel $kernel
     */
    public function __construct(string $env)
    {
        self::$env = $env;
    }

    /**
     * showTest
     *
     * @param array $options
     * @return bool
     */
    public static function showTest(array $options = []): bool
    {
        if (self::$env === $options['environment'])
            return true;
        return false;
    }

}