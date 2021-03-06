<?php

namespace MainBundle\Controller;

use MainBundle\Entity\AlbumPhoto;
use MainBundle\Entity\Photo;
use MainBundle\Type\PhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PhotoController extends Controller
{
    /**
     *
     * @Route("/new_photo", name="photo_new")
     */
    public function newAction(Request $request)
    {

        $photo = new Photo();

        $form = $this->createForm(PhotoType::class, $photo);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $fileName = md5(uniqid());
            $photo->setName($fileName);
            $em->persist($photo);
            $albums =$form['album']->getData();
            $file = $form['image']->getData(); // recuperer l image
            $tmp = $file->getPathname();

            foreach ($albums as $album){
                $albumPhoto =new AlbumPhoto();
                copy($tmp,$album->getPath().'/'.$fileName);
                $albumPhoto->setPhoto($photo);
                $albumPhoto->setAlbum($album);
                $albumPhoto->setPath($album->getPath().$fileName);
                $em->persist($albumPhoto);
            }

            $em->flush();
            $this->addFlash('success', 'The photo has been successfully added.');
            return $this->redirectToRoute('photo_new');

        }

        return $this->render('MainBundle:Photo:new.html.twig', array('photoForm' => $form->createView()));
    }
}
