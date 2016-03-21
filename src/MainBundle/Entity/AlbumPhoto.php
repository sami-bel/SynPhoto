<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlbumPhoto
 *
 * @ORM\Table(name="album_photo")
 * @ORM\Entity(repositoryClass="MainBundle\Repository\AlbumPhotoRepository")
 */
class AlbumPhoto
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="MainBundle\Entity\Album",inversedBy="photos", cascade={"remove"}))
     * @ORM\JoinColumn(nullable=false)
     */
    private $album;

    /**
     * @ORM\ManyToOne(targetEntity="MainBundle\Entity\Photo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set album
     *
     * @param Album $album
     *
     * @return AlbumPhoto
     */
    public function setAlbum(Album $album)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return string
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set photo
     *
     * @param Photo $photo
     *
     * @return AlbumPhoto
     */
    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return AlbumPhoto
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}

