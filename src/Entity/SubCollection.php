<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Repository\SubCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as Collectiond;
use App\Entity\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\SubCollection\PostOperation;
use App\Controller\SubCollection\GetCardsOperation;
use App\Controller\SubCollection\PostCardOperation;

/**
 * @ApiResource(
 *     formats={"json"={"application/json"}},
 *     normalizationContext={"groups"={"subcollection:read"}},
 *     collectionOperations={
 *         "post"={
 *              "method" = "POST",
 *              "path" = "sub_collections/{id}/cards",
 *              "controller" = PostCardOperation::class
 *         },
 *         "post_specific"={
 *              "method" = "POST",
 *              "path" = "sub_collections/{id}/cards/{idcard}",
 *              "controller" = PostCardOperation::class
 *         },
 *     },
 *     itemOperations={
 *         "get"={},
 *         "getcards"={
 *              "method" = "GET",
 *              "path" = "sub_collections/{id}/cards",
 *              "controller" = GetCardsOperation::class,
 *          },
 *         "put"={},
 *         "delete"={},
 *     }
 * )
 * @ORM\Entity(repositoryClass=SubCollectionRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class SubCollection
{

    use TimeStampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("collection:read")
     * @Groups("subcollection:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("collection:read")
     * @Groups("subcollection:read")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("collection:read")
     * @Groups("subcollection:read")
     */
    private $color;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public = 1;

    /**
     * @ORM\ManyToOne(targetEntity=Collection::class, inversedBy="subCollection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $collection;

    /**
     * @ORM\ManyToMany(targetEntity=Card::class, mappedBy="subCollection", orphanRemoval=true)
     */
    private $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return Collectiond
     */
    public function getCards(): Collectiond
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setSubCollection($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getSubCollection() === $this) {
                $card->setSubCollection(null);
            }
        }

        return $this;
    }
}
