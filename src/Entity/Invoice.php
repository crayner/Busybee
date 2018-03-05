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

    /**
     * @var null|string
     */
    private $identifier;

    /**
     * @return null|string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param null|string $identifier
     * @return Invoice
     */
    public function setIdentifier(?string $identifier): Invoice
    {
        $this->identifier = $identifier;
        return $this;
    }
}