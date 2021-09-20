<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Repository\CollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as Collectiond;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Controller\Collection\PostOperation;
use App\Controller\Collection\GetOperation;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     formats={"json"={"application/json"}},
 *     normalizationContext={"groups"={"collection:read"}},
 *     collectionOperations={
 *         "get"={
 *              "method" = "GET",
 *              "controller" = GetOperation::class
 *     },
 *         "post"={
 *              "method" = "POST",
 *              "controller" = PostOperation::class
 *         },
 *     },
 *     itemOperations={
 *         "get"={},
 *         "put"={},
 *         "delete"={},
 *     }
 * )
 * @ORM\Entity(repositoryClass=CollectionRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Collection
{

    use TimeStampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("collection:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("collection:read")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("collection:read")
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="collections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=SubCollection::class, mappedBy="collection", orphanRemoval=true)
     * @Groups("collection:read")
     */
    private $subCollection;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public = 1;

    public function __construct()
    {
        $this->subCollection = new ArrayCollection();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collectiond
     */
    public function getSubCollection(): Collectiond
    {
        return $this->subCollection;
    }

    public function addSubCollection(SubCollection $subCollection): self
    {
        if (!$this->subCollection->contains($subCollection)) {
            $this->subCollection[] = $subCollection;
            $subCollection->setCollection($this);
        }

        return $this;
    }

    public function removeSubCollection(SubCollection $subCollection): self
    {
        if ($this->subCollection->removeElement($subCollection)) {
            // set the owning side to null (unless already changed)
            if ($subCollection->getCollection() === $this) {
                $subCollection->setCollection(null);
            }
        }

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
}
