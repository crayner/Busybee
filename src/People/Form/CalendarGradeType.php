<?php
namespace App\People\Form;

use Hillrange\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

class CalendarGradeType extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
    }

    public function getBlockPrefix()
    {
        return 'calendar_grade';
    }
}