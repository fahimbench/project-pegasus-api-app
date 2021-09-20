<?php

namespace App\Controller\SubCollection;

use App\Entity\Card;
use App\Entity\Collection;
use App\Entity\SubCollection;
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

class PostCardOperation extends AbstractController
{


    public function __construct()
    {
    }

    public function __invoke(int $id, $idcard = null, Request $request, TokenStorageInterface $tokenStorage, EntityManagerInterface $em){
        $user = $tokenStorage->getToken()->getUser();
        $rContent = json_decode($request->getContent());

        if(empty($idcard)){
            if(!isset($rContent->idCard) || !is_int($rContent->idCard) || $rContent->idCard < 1){
                return new Response(json_encode(["message"=>"Doit contenir l'id d'une carte existante sur l'api cards"]), 400,["Content-Type" => "application/json"]);
            }
            if(!isset($rContent->amount) || !is_int($rContent->amount) || $rContent->amount < 1){
                return new Response(json_encode(["message"=>"Doit contenir une quantité de carte"]), 400,["Content-Type" => "application/json"]);
            }
            $getSubCollection = $em->getRepository(SubCollection::class)->getByIdAndOwner($user,$id);
            if($getSubCollection){
                $card = new Card();
                $card->setSubCollection($getSubCollection);
                $card->setIdCard($rContent->idCard);
                $card->setAmount($rContent->amount);
                $card->setFirstEdition($rContent->firstEdition ?? 0);
                $em->persist($card);
                $em->flush();
                return new Response(json_encode(["message"=>"Carte ajouté avec succés"]), 204,["Content-Type" => "application/json"]);
            }else{
                return new Response(json_encode(["message"=>"Une erreur est survenu"]), 400,["Content-Type" => "application/json"]);
            }
        }else{
            if(isset($rContent->idCard) && (!is_int($rContent->idCard) || $rContent->idCard < 1)){
                return new Response(json_encode(["message"=>"Doit contenir l'id d'une carte existante sur l'api cards"]), 400,["Content-Type" => "application/json"]);
            }
            if(isset($rContent->amount) && (!is_int($rContent->amount) || $rContent->amount < 1)){
                return new Response(json_encode(["message"=>"Doit contenir une quantité de carte"]), 400,["Content-Type" => "application/json"]);
            }
            $getCard = $em->getRepository(Card::class)->getByIdSubAndOwner($user,$id, $idcard);
            if($getCard){
                if(isset($rContent->idCard)){
                    $getCard->setIdCard($rContent->idCard);
                }
                if(isset($rContent->amount)){
                    $getCard->setAmount($rContent->amount);
                }
                if(isset($rContent->firstEdition)){
                    $getCard->setFirstEdition($rContent->firstEdition);
                }
                $em->persist($getCard);
                $em->flush();
                return new Response(json_encode(["message"=>"Carte update avec succés"]), 204,["Content-Type" => "application/json"]);
            }else{
                return new Response(json_encode(["message"=>"Une erreur est survenu"]), 400,["Content-Type" => "application/json"]);
            }
        }

    }
}
