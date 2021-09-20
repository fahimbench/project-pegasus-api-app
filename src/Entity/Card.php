<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @ApiResource(
 *     formats={"json"={"application/json"}},
 *     normalizationContext={"groups"={"card:read"}},
 *     collectionOperations={
 *     },
 *     itemOperations={
 *         "get"={},
 *         "put"={},
 *         "delete"={},
 *     }
 * )
 * @ORM\Entity(repositoryClass=CardRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Card
{
    use TimeStampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("subcollection:read")
     * @Groups("card:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SubCollection::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subCollection;

    /**
     * @ORM\Column(type="integer")
     * @Groups("subcollection:read")
     * @Groups("card:read")
     */
    private $idCard;

    /**
     * @ORM\Column(type="integer")
     * @Groups("subcollection:read")
     * @Groups("card:read")
     */
    private $amount = 1;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("subcollection:read")
     * @Groups("card:read")
     */
    private $firstEdition = 0;

    /**
    * @Groups("subcollection:read")
    * @Groups("card:read")
    */
    private $info;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubCollection(): ?SubCollection
    {
        return $this->subCollection;
    }

    public function setSubCollection(?SubCollection $subCollection): self
    {
        $this->subCollection = $subCollection;

        return $this;
    }

    public function getIdCard(): ?int
    {
        return $this->idCard;
    }

    public function setIdCard(int $idCard): self
    {
        $this->idCard = $idCard;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getFirstEdition(): ?bool
    {
        return $this->firstEdition;
    }

    public function setFirstEdition(bool $firstEdition): self
    {
        $this->firstEdition = $firstEdition;

        return $this;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function getInfo()
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $_ENV["PROJECT_PEGASUS_API_CARDS_HOST"].'/api/cards/'.$this->getIdCard(), [
            'headers' => [
                'X-AUTH-TOKEN' => $_ENV["PROJECT_PEGASUS_API_CARDS_APIKEY"],
            ],
        ]);

        if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300){
            $this->info = json_decode($response->getContent(), true);
            return $this->info;
        }else{
            $this->info = null;
            return $this->info;
        }
    }

    public function setInfo($info): self
    {
        $this->info = $info;

        return $this;
    }

}
