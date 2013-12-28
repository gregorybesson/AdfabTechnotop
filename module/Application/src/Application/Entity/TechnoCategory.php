<?php
namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * An topSite represents an Alexa Country Top Site stat.
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="techno_category")
 */
class TechnoCategory
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     */
    protected $id;

    /**
     * @ORM\Column(type="string");
     */
    protected $label;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Techno", mappedBy="categories")
     */
    protected $technos;
    
    /**
     * @ORM\Column(name="display_menu", type="boolean");
     */
    protected $displayMenu=1;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    public function __construct($id)
    {
        $this->id = $id;
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
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @return the $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return the unknown_type
     */
    public function getTechnos() {
        return $this->technos;
    }

    /**
     * @param unknown_type $technos
     */
    public function setTechnos($technos) {
        $this->technos = $technos;

        return $this;
    }

    public function addTechno(\Application\Entity\Techno $techno)
    {
        $this->technos[] = $techno;
    }

	/**
     * @return the $displayMenu
     */
    public function getDisplayMenu()
    {
        return $this->displayMenu;
    }

	/**
     * @param field_type $displayMenu
     */
    public function setDisplayMenu($displayMenu)
    {
        $this->displayMenu = $displayMenu;
    }

	/**
     * @param field_type $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
