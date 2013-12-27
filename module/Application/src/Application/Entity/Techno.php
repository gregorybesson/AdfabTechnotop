<?php
namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="techno")
 */
class Techno
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string");
     */
    protected $techno;

    /**
     * @ORM\Column(type="string");
     */
    protected $website;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\TechnoCategory", inversedBy="technos")
     * @ORM\JoinTable(name="technos_categories",
     *      joinColumns={@ORM\JoinColumn(name="techno", referencedColumnName="techno")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    protected $categories;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    public function __construct($techno)
    {
        $this->techno = $techno;
        $this->categories = new ArrayCollection();
    }

     /**
     * @return the unknown_type
     */
    public function getTechno() {

     return $this->techno;
    }

     /**
     * @param unknown_type $techno
     */
    public function setTechno($techno) {
     $this->techno = $techno;

     return $this;
    }

     /**
     * @return the unknown_type
     */
    public function getWebsite() {

     return $this->website;
    }

     /**
     * @param unknown_type $website
     */
    public function setWebsite($website) {
     $this->website = $website;

     return $this;
    }

     /**
     * @return the unknown_type
     */
    public function getCategories() {

     return $this->categories;
    }

     /**
     * @param unknown_type $categories
     */
    public function setCategories($categories) {
     $this->categories = $categories;

     return $this;
    }

    public function addCategories(\Doctrine\Common\Collections\ArrayCollection $categories){
        foreach ($categories as $category) {
            $this->categories->add($category);
        }
    }

    public function removeCategories(\Doctrine\Common\Collections\ArrayCollection $categories){
        foreach ($categories as $category) {
            $this->categories->removeElement($category);
        }
    }

    public function addCategory(\Application\Entity\TechnoCategory $category)
    {
        $category->addTechno($this); // synchronously updating inverse side
        $this->categories[] = $category;
    }

    /** @PrePersist */
    public function createChrono(){
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
