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


class PhotoController extends Controller
{


    /**
     * @Route("/api/photo" )
     */
    public function photoAction(Request $request){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $body =$request->getContent();

        $data = json_decode($body, true);
        $idAlbum =$data['album'];


        $album = $this->getDoctrine()->getRepository('MainBundle:Album')->find($idAlbum);

        $resultat =array();
        $path =$this->getPath($album);

        if ($path != null){

            $pathSer = $serializer->serialize($path, 'json');
         //   $pathSer = json_encode($path,true);
        }

        else {$rps ='l album n existe pas ' ;
            $pathSer = json_encode($rps,true);
        }

        $response = new Response($pathSer);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        //return new Response('ok');
    }


    private function getPath($Album)
    {
        $listPath = array();
        $album = $this->getDoctrine()->getRepository('MainBundle:AlbumPhoto')->findBy(array('album' =>$Album));

        foreach ($album as $a)
        {

            $listPath[]=$a->getPath() ;

        }

        return $listPath;
    }



}
