<?php
namespace App\People\Form;

use Hillrange\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

class CalendarGradeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'calendar_grade';
    }
}