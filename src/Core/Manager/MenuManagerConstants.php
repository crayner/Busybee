<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 3/01/2018
 * Time: 18:10
 */

namespace App\Core\Manager;


class MenuManagerConstants
{
	CONST NODES = '
9:
    name: Miscellaneous
    label: menu.Misc
    order: 9
    menu: 9
        ';

	CONST ITEMS = '
90:
    label: menu.templateDesign
    name: Template Design
    route: home_template
    parameters: []
    node: 9
    order: 90
91:
    label: menu.acknowledgement
    name: Template Design
    route: acknowledgement
    parameters: []
    node: 9
    order: 91
	
	';


}