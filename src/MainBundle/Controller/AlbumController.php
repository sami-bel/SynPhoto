<?php

namespace MainBundle\Controller;

use MainBundle\Entity\AlbumPhoto;
use MainBundle\Type\AlbumType;
use MainBundle\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Csrf\CsrfToken;


/**
 * @Route("/album")
 */

class AlbumController extends Controller
{
    /**
     *
     * @Route("/new", name="album_new")
     */
    public function newAction(Request $request)
    {
        $file_system = new Filesystem();
        $path = '../web/albums/';
        $album =new Album();
        $form =$this->createForm(AlbumType::class,$album);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $name= $form['titre']->getData();

                $file_system->mkdir($path.$name);

                $em = $this->getDoctrine()->getManager();
                $album->setPath('albums/'.$name.'/');

                $em->persist($album);
                $em->flush();
                $this->addFlash('success', 'The albums has been successfully added.');
                return $this->redirectToRoute('album_new');

            } catch (IOExceptionInterface $e) {
                echo "An error occurred while creating your directory at ".$e->getPath();
            }
        }

        return $this->render('MainBundle:Albums:new.html.twig', array('albumForm' => $form->createView()));
    }



    /**
     * @Route(
     *     "/{id}",
     *     name="album_update",
     *     requirements={ "id" = "\d+"}
     * )
     *
     */
    public function updateAction(Request $request, Album $album)
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The album has been successfully updated.');

            return $this->redirectToRoute(
                'album_update',
                array('id' => $album->getId())
            );
        }

        return $this->render('MainBundle:Albums:new.html.twig',  array('albumForm' => $form->createView()));
    }

    /**
     * @Route("/", name="albums_list")
     *
     */
    public function listAction()
    {
        $albums = $this->getDoctrine()->getRepository('MainBundle:Album')->findAll();

        return $this->render('MainBundle:Albums:list.html.twig',array('albums' => $albums));
    }

    /**
     * @Route("/delete", name="albums_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request)
    {

        if (!$albums = $this->getDoctrine()->getRepository('MainBundle:Album')->findOneById($request->request->get('album_id'))) {
            $this->addFlash('error', 'The article you want to delete does not exist.');

            return $this->redirectToRoute('albums_list');
        }


        $csrf_token = new CsrfToken('delete_album', $request->request->get('_csrf_token'));

        if ($this->get('security.csrf.token_manager')->isTokenValid($csrf_token)) {
            $em = $this->getDoctrine()->getManager();
            $path = $albums->getPath();
//
            if(!$this->delete_directory($path))
            {
                if(!rmdir($path))
                dump ("Could not remove $path");
            }
            $em->remove($albums);
            $em->flush();

            $this->addFlash('success', 'You have successfully deleted the article.');
        } else {
            $this->addFlash('error', 'An error occurred.');
        }

        return $this->redirectToRoute('albums_list');
    }

    /**
     * @Route("/photoalbum/{id}", name="photo_list")
     *
     */
    public function listPhotoAction($id)
    {
        $photos = $this->getDoctrine()->getRepository('MainBundle:AlbumPhoto')->findPhotoAlbum($id);


        return $this->render('MainBundle:Albums:listPhoto.html.twig',array('photos' => $photos));
    }


    public function delete_directory($dirname) {
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname . "/" . $file))
                        unlink($dirname . "/" . $file);
                    else
                        $this->delete_directory($dirname . '/' . $file);
                }
            }
            closedir($dir_handle);
            rmdir($dirname);
            return true;
        }
        else
            return false;
    }

}
