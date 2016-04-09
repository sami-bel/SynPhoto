<?php

namespace WebServiceBundle\Controller;

use MainBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class AController extends Controller
{

    /**
     * @Route("/api/albumm" )
     */
    public function albumAction(){

        return new Response('ok');
    }

    private function getAlbum(){
        $album = $this->getDoctrine()->getRepository('WebServiceBundle:Album')->findall();
        return $album;
    }



}