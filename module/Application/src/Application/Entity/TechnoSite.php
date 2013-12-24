<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * An topSite represents an Alexa Country Top Site stat.
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="techno_site")
 */
class TechnoSite
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string");
     */
    protected $url;

    /**
     * @ORM\Id
     * @ORM\Column(type="string");
     */
    protected $techno;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    public function __construct($url, $techno)
    {
        $this->url = $url;
        $this->techno = $techno;
    }

    /** @PrePersist */
    public function createChrono()
    {
        $this->created_at = new \DateTime("now");
        $this->updated_at = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updated_at = new \DateTime("now");
    }

	/**
     * @return the $url
     */
    public function getUrl()
    {
        return $this->url;
    }

	/**
     * @param field_type $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

	/**
     * @return the $techno
     */
    public function getTechno()
    {
        return $this->techno;
    }

	/**
     * @param field_type $techno
     */
    public function setTechno($techno)
    {
        $this->techno = $techno;

        return $this;
    }

	/**
     * @return the $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return the $updated_at
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        /*$this->title = $data['title'];
        $this->identifier = $data['identifier'];
        $this->welcome_block = $data['welcome_block'];*/
    }
}
