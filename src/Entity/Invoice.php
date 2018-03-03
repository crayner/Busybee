<?php
namespace App\Entity;


use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class Invoice implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @var null|int
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}