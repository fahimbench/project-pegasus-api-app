<?php

namespace App\Controller\Collection;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Collection;
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

class GetOperation extends AbstractController
{


    public function __construct()
    {
    }

    public function __invoke(Paginator $data)
    {
        return $this->pagination($data);
    }

    protected function pagination(Paginator $data){
        $json = [
            "count" => $data->count(),
            "currentPage" => $data->getCurrentPage(),
            "itemsPerPage" => $data->getItemsPerPage(),
            "lastPage" => $data->getLastPage(),
            "totalItems" => $data->getTotalItems(),
            "data" => $data
        ];
        return $json;
    }
}
