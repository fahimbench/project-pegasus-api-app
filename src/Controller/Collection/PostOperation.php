<?php

namespace App\Controller\Collection;

use App\Entity\Collection;
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

class PostOperation extends AbstractController
{


    public function __construct()
    {
    }

    public function __invoke(CollectionRepository $collectionRepository, Request $request, TokenStorageInterface $tokenStorage, EntityManagerInterface $em){
        $user = $tokenStorage->getToken()->getUser();
        $rContent = json_decode($request->getContent());
        if(empty($rContent->name)){
            return new Response(json_encode(["message"=>"Nom d'une collection ne peut être vide ou null"]), 400,["Content-Type" => "application/json"]);
        }
        $collection = new Collection();
        $collection->setName($rContent->name);
        $collection->setColor($rContent->color);
        $collection->setOwner($user);
        $em->persist($collection);
        $em->flush();
        return new Response(json_encode(["message"=>"Création de la collection validé !"]), 204,["Content-Type" => "application/json"]);
    }
}
