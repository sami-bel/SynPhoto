<?php

namespace MainBundle\Controller;

use MainBundle\Entity\AlbumPhoto;
use MainBundle\Entity\User;
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
        $path = 'albums/';
        $album =new Album();
        $form =$this->createForm(AlbumType::class,$album);

        $form->handleRequest($request);


        if ($form->isValid()) {
            try {
                $name= $form['titre']->getData();

                $file_system->mkdir($path.$name);
//                $file_system->mkdir('albums/test22');


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

    /**
     * @Route("/subscribe", name="albums_subscribe")
     * @Method("POST")
     */
    public function SubscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$album = $this->getDoctrine()->getRepository('MainBundle:Album')->findOneById($request->request->get('album_id'))) {
            $this->addFlash('error', 'The album you want to delete does not exist.');

            return $this->redirectToRoute('albums_list');
        }
        if (!$user = $this->getDoctrine()->getRepository('MainBundle:User')->findOneById($request->request->get('user_id'))) {
            $this->addFlash('error', 'The user you want to delete does not exist.');

            return $this->redirectToRoute('albums_list');
        }

        if ( !$user->getAlbums()->contains($album)) {
            $user->addAlbum($album);
            $em->persist($user);
            $em->flush();
        }
        else {
            $this->addFlash('error', 'your were subscribed in this album .');
            return $this->redirectToRoute('albums_list');

        }

        return $this->redirectToRoute('albums_list');

    }

    /**
     * @Route("/albums_user/{id}", name="albums_user_list")
     *
     */
    public function listAlbumUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('MainBundle:User')->find($id);
        $albums = $user->getAlbums();
        return $this->render('MainBundle:Albums:listAlbumsUser.html.twig',array('albums' => $albums));
    }

    /**
     *@Route("/unsubscribe", name="albums_unsubscribe")
     *@Method("POST")
     */
    public function  unsubscribeAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();

        if (!$album = $this->getDoctrine()->getRepository('MainBundle:Album')->findOneById($request->request->get('album_id'))) {
            $this->addFlash('error', 'The album you want to delete does not exist.');

            return $this->redirectToRoute('albums_list');
        }


        if (!$user = $this->getDoctrine()->getRepository('MainBundle:User')->findOneById($request->request->get('user_id'))) {
            $this->addFlash('error', 'The user you want to delete does not exist.');

            return $this->redirectToRoute('albums_list');
        }

        $user->removeAlbum($album);

        $em->persist($user);

        $em->flush();
        return $this->redirectToRoute('albums_user_list', array('id'=>$user->getID()));
    }

    /**
     * @param $dirname
     * @return bool
     */
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
