<?php
namespace App\Controller;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    public function me(Request $request): Response
    {
        $user = [
            "username" => $this->getUser()->getUserIdentifier(),
            "email" => $this->getUser()->getEmail(),
            "role" => $this->getUser()->getRoles()
        ];

        return new Response(json_encode($user), 200,["Content-Type" => "application/json"]);
    }

    public function register(Request $request, UserPasswordHasherInterface $encoder): Response
    {

        $em = $this->getDoctrine()->getManager();

        $r = json_decode($request->getContent());

        if($em->getRepository(User::class)->findBy(['email' => $r->email])){
            return new Response(json_encode(["message"=> "Cet email est déjà utilisé."]), 400,["Content-Type" => "application/json"]);
        }
        if($em->getRepository(User::class)->findBy(['username' => $r->username])){
            return new Response(json_encode(["message"=>"Ce nom d'utilsateur est déjà utilisé."]), 400,["Content-Type" => "application/json"]);
        }
        if(strlen($r->password) < 6 || strlen($r->password) > 255){
            return new Response(json_encode(["message"=>"Le mot de passe doit être compris entre 6 et 255 caractères"]), 400,["Content-Type" => "application/json"]);
        }
        $user = new User();
        $user->setEmail($r->email);
        $user->setUsername($r->username);
        $user->setPassword($encoder->hashPassword($user, $r->password));
        $em->persist($user);
        $em->flush();
        return new Response(json_encode(["message"=>"Création du compte effectué, vous pouvez vous connecter !"]), 200,["Content-Type" => "application/json"]);
    }

    public function api(): Response
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUserIdentifier()));
    }

    public function changePassword(Request $request, UserPasswordHasherInterface $encoder, UserInterface $user): Response
    {

        $em = $this->getDoctrine()->getManager();

        $r = json_decode($request->getContent());

        if(!$encoder->isPasswordValid($this->getUser(), $r->old_password)){
            return new Response(json_encode(["message"=>"L'ancien mot de passe ne correspond pas."]), 400,["Content-Type" => "application/json"]);
        }

        if($encoder->isPasswordValid($this->getUser(), $r->new_password)){
            return new Response(json_encode(["message"=>"Le nouveau mot de passe est identique à l'ancien."]), 400,["Content-Type" => "application/json"]);
        }

        if(strlen($r->new_password) < 6 || strlen($r->new_password) > 255){
            return new Response(json_encode(["message"=>"Le nouveau mot de passe doit être compris entre 6 et 255 caractères"]), 400,["Content-Type" => "application/json"]);
        }


        $user = $this->getUser();
        $user->setPassword($encoder->hashPassword($user, $r->new_password));
        $em->persist($user);
        $em->flush();
        return new Response(json_encode(["message"=>"Changement du mot de passe validé !"]), 200,["Content-Type" => "application/json"]);
    }
}