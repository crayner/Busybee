<?php
namespace App\School\Util;

use App\Entity\ExternalActivity;
use Symfony\Component\Translation\TranslatorInterface;

class ActivityManager
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ActivityManager constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ExternalActivity $activity
     * @return string
     */
    public function getTermsGrades(ExternalActivity $activity): string
    {
        $result = '';
        if ($activity->getTerms()->count() == 0)
            $result .= $this->translator->trans('All Terms', [], 'School');

        foreach($activity->getTerms()->getIterator() as $term)
            $result .= $term->getNameShort() . ', ';

        $result = trim($result, ', '). "<br />\n";

        if ($activity->getCalendarGrades()->count() > 0)
        {
            foreach($activity->getCalendarGrades()->getIterator() as $grade)
                $result .= $grade->getGrade(). ', ';
        } elseif ($activity->getCalendarGrades()->count() == 0)
            $result .= $this->translator->trans('All Grades', [], 'School');

        $result = trim($result, ', ');

        return $result;
    }
}