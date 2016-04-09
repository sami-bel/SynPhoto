<?php

namespace WebServiceBundle\Controller;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AlbumController extends Controller
{

    /**
     * @Route("/api/album" )
     */
    public function albumAction(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $albums =$this->getAlbum();

        if ($albums != null){

            $albumsSer = $serializer->serialize($albums, 'json');

        }

        else {$rps ='l utilisateur n existe pas ' ;
            $albumsSer = json_encode($rps,true);
        }

        $response = new Response($albumsSer);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        //return new Response('ok');
    }

    private function getAlbum(){
        $album = $this->getDoctrine()->getRepository('MainBundle:Album')->findall();
        return $album;
    }



}
