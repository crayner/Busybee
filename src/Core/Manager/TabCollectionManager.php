<?php
namespace App\Core\Manager;

use Hillrange\Form\Util\CollectionManager;

abstract class TabCollectionManager extends CollectionManager implements TabManagerInterface
{
    use TabManagerTrait;
}