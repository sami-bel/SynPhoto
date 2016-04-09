<?php

namespace MainBundle\Controller;

use MainBundle\Entity\AlbumPhoto;
use MainBundle\Entity\Photo;
use MainBundle\Type\PhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;

class PhotoController extends Controller
{
    /**
     *
     * @Route("/newp", name="photo_new")
     */
    public function newAction(Request $request)
    {

        $photo = new Photo();
        $albumPhoto =new AlbumPhoto();
        $form = $this->createForm(PhotoType::class, $photo);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $albums =$form['album']->getData();

            $fileName = md5(uniqid());
            foreach ($albums as $album){
            $form['image']->getData()->move($album->getPath(),$fileName);
            }
            $photo->setName($fileName);
            $albumPhoto->setPhoto($photo);
            $albumPhoto->setAlbum($album);
            $albumPhoto->setPath($album->getPath().$fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->persist($albumPhoto);
            $em->flush();
            $this->addFlash('success', 'The albums has been successfully added.');
            return $this->redirectToRoute('photo_new');

        }

        return $this->render('MainBundle:Photo:new.html.twig', array('photoForm' => $form->createView()));
    }
}
