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
 * Date: 31/05/2018
 * Time: 09:13
 */

namespace App\Core\Util;


class SettingChoiceGenerator
{
    /**
     * generateChoices
     *
     * @param string $key
     * @param array $data
     * @param null|string $useName
     * @return array
     */
    public static function generateChoices(string $key = '', array $data = [], ?string $useName = null)
    {
        $results = [];
        foreach($data as $name => $value)
        {
            if ($name === $value)
                $results[rtrim($key. '.' . $value, '.')] = $value;
            elseif (strval(intval($name)) !== trim($name) && ! is_array($value))
                $results[$name] = ltrim($key. '.' . $value, '.');
            elseif (is_array($value))
                if (! empty($useName) && !empty($value[$useName]))
                    $results[strtolower(ltrim($key. '.' . $value[$useName], '.'))] = $value[$useName] ;
                else
                    $results[$name] = self::generateChoices($key, $value);
            else
                if (strval(intval($name)) !== trim($name))
                    $results[ltrim($key. '.' . $value, '.')] = $name ;
                else
                    if (! empty($value))
                        $results[ltrim($key. '.' . $value, '.')] = $value;

        }
        return $results;
    }
}