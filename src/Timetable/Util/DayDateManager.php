<?php
namespace App\Timetable\Util;

use App\Core\Exception\MissingClassException;
use App\Timetable\Organism\DayDate;

class DayDateManager extends TimetableManager
{
    /**
     * @return array
     */
    public function getTabs(): array
    {
        if (! $this->isTimetableExists())
            throw new MissingClassException('The timetable was not found.');

        $tabs = [];
        foreach($this->getTimetable()->getCalendar()->getTerms()->getIterator() as $term)
        {
            $tab = [];
            $tab['label'] = $term->getName();
            $tab['include'] = 'Timetable/Term/term.html.twig';
            $tab['translation'] = false;
            $tab['with']['id'] = $term->getId();
            $tabs[$term->getName()] = $tab;
        }
        return $tabs;
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        $request = $this->getStack()->getCurrentRequest();
        $xx = '';

        foreach($this->getTimetable()->getCalendar()->getTerms()->getIterator() as $term) {
            $target = 'term' . $term->getId() . 'Collection';
            $xx = "manageCollection('" . $this->getRouter()->generate("timetable_day_date_term_display", ["id" => $request->get("id"), "cid" => $term->getId()]) . "',$target, '')\n";
        }

        return $xx;
    }

    /**
     * @var DayDate|null
     */
    private $dayDate;

    /**
     * @return DayDate|null
     */
    public function getDayDate(): ?DayDate
    {
        if (empty($this->dayDate))
            $this->dayDate =  new DayDate($this);
        return $this->dayDate;
    }

    /**
     * @param DayDate|null $dayDate
     * @return DayDateManager
     */
    public function setDayDate(?DayDate $dayDate): DayDateManager
    {
        $this->dayDate = $dayDate;
        return $this;
    }
}