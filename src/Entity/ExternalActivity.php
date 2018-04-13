<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * External Activity
 */
class ExternalActivity extends Activity
{
    /**
     * @var boolean
     */
    private $active;

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $this->active = $this->active ? true : false;

        return $this->active;
    }

    /**
     * @param null|bool $active
     * @return Activity
     */
    public function setActive(?bool $active): ExternalActivity
    {
        $this->active = $active ? true : false;

        return $this;
    }

    /**
     * @var bool
     */
    private $registration;

    /**
     * @return bool
     */
    public function getRegistration(): bool
    {
        $this->registration = $this->registration ? true : false ;

        return $this->registration;
    }

    /**
     * @param mixed $registration
     * @return ExternalActivity
     */
    public function setRegistration(bool $registration): ExternalActivity
    {
        $this->registration = $registration ? true : false ;

        return $this;
    }

    /**
     * @var null|string
     */
    private $provider;

    /**
     * @return null|string
     */
    public function getProvider(): ?string
    {
        $this->provider = $this->provider ?: 'school';

        return strtolower($this->provider);
    }

    /**
     * @param null|string $provider
     * @return ExternalActivity
     */
    public function setProvider(?string $provider): ExternalActivity
    {
        $this->provider = $provider ?: 'school';

        return $this;
    }

    /**
     * @var null|Collection
     */
    private $terms;

    /**
     * @return Collection|null
     */
    public function getTerms(): ?Collection
    {
        $this->terms = $this->terms ?: new ArrayCollection();

        if ($this->terms instanceof PersistentCollection)
            $this->terms->initialize();

        return $this->terms;
    }

    /**
     * @param Collection|null $terms
     * @return ExternalActivity
     */
    public function setTerms(?Collection $terms): ExternalActivity
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * @param Term|null $term
     * @return ExternalActivity
     */
    public function addTerm(?Term $term): ExternalActivity
    {
        if (empty($term) || $this->getTerms()->contains($term))
            return $this;

        $this->terms->add($term);

        return $this;
    }

    /**
     * @param Term|null $term
     * @return ExternalActivity
     */
    public function removeTerm(?Term $term): ExternalActivity
    {
        if (empty($term) || ! $this->getTerms()->contains($term))
            return $this;

        $this->terms->removeElement($term);

        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $listingStart;

    /**
     * @return \DateTime|null
     */
    public function getListingStart(): ?\DateTime
    {
        return $this->listingStart;
    }

    /**
     * @param \DateTime|null $listingStart
     * @return ExternalActivity
     */
    public function setListingStart(?\DateTime $listingStart): ExternalActivity
    {
        $this->listingStart = $listingStart;

        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $listingEnd;

    /**
     * @return \DateTime|null
     */
    public function getListingEnd(): ?\DateTime
    {
        return $this->listingEnd;
    }

    /**
     * @param \DateTime|null $listingEnd
     * @return ExternalActivity
     */
    public function setListingEnd(?\DateTime $listingEnd): ExternalActivity
    {
        $this->listingEnd = $listingEnd;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $programStart;

    /**
     * @return \DateTime|null
     */
    public function getProgramStart(): ?\DateTime
    {
        return $this->programStart;
    }

    /**
     * @param \DateTime|null $programStart
     * @return ExternalActivity
     */
    public function setProgramStart(?\DateTime $programStart): ExternalActivity
    {
        $this->programStart = $programStart;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $programEnd;

    /**
     * @return \DateTime|null
     */
    public function getProgramEnd(): ?\DateTime
    {
        return $this->programEnd;
    }

    /**
     * @param \DateTime|null $programEnd
     * @return ExternalActivity
     */
    public function setProgramEnd(?\DateTime $programEnd): ExternalActivity
    {
        $this->programEnd = $programEnd;
        return $this;
    }

    /**
     * @var null|int
     */
    private $maxParticipants;

    /**
     * @return int|null
     */
    public function getMaxParticipants(): ?int
    {
        return intval($this->maxParticipants);
    }

    /**
     * @param int|null $maxParticipants
     * @return ExternalActivity
     */
    public function setMaxParticipants(?int $maxParticipants): ExternalActivity
    {
        $this->maxParticipants = intval($maxParticipants);
        return $this;
    }

    /**
     * @var null|string
     */
    private $description;

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return ExternalActivity
     */
    public function setDescription(?string $description): ExternalActivity
    {
        $this->description = $description ?: null;

        return $this;
    }

    /**
     * @var null|float
     */
    private $payment;

    /**
     * @return float|null
     */
    public function getPayment(): ?float
    {
        return $this->payment;
    }

    /**
     * @param float|null $payment
     * @return ExternalActivity
     */
    public function setPayment(?float $payment): ExternalActivity
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @var null|string
     */
    private $paymentType;

    /**
     * @return null|string
     */
    public function getPaymentType(): string
    {
        return $this->paymentType ?: 'program';
    }

    /**
     * @param null|string $paymentType
     * @return ExternalActivity
     */
    public function setPaymentType(?string $paymentType): ExternalActivity
    {
        $this->paymentType = $paymentType ?: 'program' ;

        return $this;
    }

    /**
     * @var null|string
     */
    private $paymentFirmness;

    /**
     * @return null|string
     */
    public function getPaymentFirmness(): ?string
    {
        return $this->paymentFirmness ?: 'finalised';
    }

    /**
     * @param null|string $paymentFirmness
     * @return ExternalActivity
     */
    public function setPaymentFirmness(?string $paymentFirmness): ExternalActivity
    {
        $this->paymentFirmness = $paymentFirmness ?: 'finalised';

        return $this;
    }

    /**
     * @var null|string
     */
    private $type;

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return ExternalActivity
     */
    public function setType(?string $type): ExternalActivity
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @var null|Collection
     */
    private $activitySlots;

    /**
     * @return Collection
     */
    public function getActivitySlots(): Collection
    {
        if (empty($this->activitySlots))
            $this->activitySlots =  new ArrayCollection();

        if ($this->activitySlots instanceof PersistentCollection && ! $this->activitySlots->isInitialized())
            $this->activitySlots->initialize();

        return $this->activitySlots;
    }

    /**
     * @param Collection|null $activitySlots
     * @return ExternalActivity
     */
    public function setActivitySlots(?Collection $activitySlots): ExternalActivity
    {
        $this->activitySlots = $activitySlots;

        return $this;
    }

    /**
     * @param ActivitySlot|null $slot
     * @param bool $add
     * @return ExternalActivity
     */
    public function addActivitySlot(?ActivitySlot $slot, $add = true): ExternalActivity
    {
        if (empty($slot))
            return $this;

        if ($add)
            $slot->setActivity($this, false);

        if ($this->getActivitySlots()->contains($slot))
            return $this;

        $this->activitySlots->add($slot);

        return $this;
    }

    /**
     * @param ActivitySlot $slot
     * @return ExternalActivity
     */
    public function removeActivitySlot(ActivitySlot $slot): ExternalActivity
    {
        $this->getActivitySlots()->removeElement($slot);

        return $this;
    }
}
