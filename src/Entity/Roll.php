<?php
namespace App\Entity;

/**
 * Roll
 */
class Roll extends Activity
{
    /**
     * @var null|Roll
     */
    private $nextRoll;

    /**
     * @return Roll|null
     */
    public function getNextRoll(): ?Roll
    {
        return $this->nextRoll;
    }

    /**
     * @param Roll|null $nextRoll
     * @return Roll
     */
    public function setNextRoll(?Roll $nextRoll): Roll
    {
        $this->nextRoll = $nextRoll;
        return $this;
    }
}
