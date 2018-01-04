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
    label: menu.miscellaneous
    order: 9
    menu: 9
    route: acknowledgement
';

	CONST ITEMS = '
90:
    label: menu.template_design
    name: Template Design
    route: home_template
    parameters: []
    node: 9
    order: 90
91:
    label: menu.acknowledgement
    name: System Acknowledgement
    route: acknowledgement
    parameters: []
    node: 9
    order: 91
';


}