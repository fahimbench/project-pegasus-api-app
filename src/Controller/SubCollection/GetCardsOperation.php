<?php

namespace App\Controller\SubCollection;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Collection;
use App\Entity\SubCollection;
use App\Repository\CardRepository;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetCardsOperation extends AbstractController
{


    public function __construct()
    {
    }

    public function __invoke(int $id, CardRepository $cardRepository){
        return $this->pagination($cardRepository->getAllBySub($id));
    }

    protected function pagination(Paginator $data){
        $json = [
            "count" => $data->count(),
            "totalItems" => $data->getTotalItems(),
            "data" => $data
        ];
        return $json;
    }

}
