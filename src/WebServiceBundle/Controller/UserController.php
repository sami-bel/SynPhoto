<?php

namespace WebServiceBundle\Controller;

use MainBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class UserController extends Controller
{
    /**
     * @Route("/api/service" )
     */
    public function newAction(Request $request)

    {

        $body =$request->getContent();
        $data = json_decode($body, true);
        $mail =$data['mail'];
        $pswd =$data['password'];

        $resultat =array();
        /**
         * tester l existance d'un user
         */
        $user = $this->isUser($mail,$pswd);

        if(!is_null($user))
        {
            $resultat ['resultat'] = $this->tokenGenerate();
            $resultat ['id']= $user->getId();
            $resultat ['nom']= $user->getNom();
            $resultat ['prenom']= $user->getPrenom();
        }
        else $resultat ['resultat'] ='erreur';

        //$data = json_encode(array("resultat"=>$resultat,),true);
        $data = json_encode($resultat,true);
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /*
     * Verifier si l utilisateur existe et le mot de passe est bon ?
     */
    private function isUser($mail, $paswd){
        $user = $this->getDoctrine()->getRepository('MainBundle:User')->findOneByEmail($mail);
        if (is_object($user)){

            $p = $user->getPassword();

            if (password_verify('lmd041188', $paswd)) $password = true;
             else $password =false;

            if ($password)
            {

                return $user;

            }
            else return null;

        }
        else null;
    }

    /*
     * Pour generer un token
     */
    private function tokenGenerate(){


        $random = random_bytes(10);
        return 'token';
    }



}
