<?php
namespace AlexaTopSites\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * An topSite represents an Alexa Country Top Site stat.
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="alexa_top_site")
 */
class TopCountry
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
    protected $country;

    /**
     * @ORM\Column(type="bigint", nullable=false)
     */
    protected $rank;
    
    /**
     * @ORM\Column(name="country_rank", type="bigint")
     */
    protected $countryRank;
    
    /**
     * @ORM\Column(name="country_reach_per_million", type="bigint")
     */
    protected $countryReachPerMillion;
    
    /**
     * @ORM\Column(name="country_page_views_per_million", type="bigint", nullable=true)
     */
    protected $countryPageViewsPerMillion;
    
    /**
     * @ORM\Column(name="country_page_views_per_user", type="bigint", nullable=true)
     */
    protected $countryPageViewsPerUser;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    public function __construct($url, $country)
    {
        $this->url = $url;
        $this->country = $country;
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
    }

	/**
     * @return the $country
     */
    public function getCountry()
    {
        return $this->country;
    }

	/**
     * @param field_type $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

	/**
     * @return the $rank
     */
    public function getRank()
    {
        return $this->rank;
    }

	/**
     * @param field_type $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

	/**
     * @return the $countryRank
     */
    public function getCountryRank()
    {
        return $this->countryRank;
    }

	/**
     * @param field_type $countryRank
     */
    public function setCountryRank($countryRank)
    {
        $this->countryRank = $countryRank;
    }

	/**
     * @return the $countryReachPerMillion
     */
    public function getCountryReachPerMillion()
    {
        return $this->countryReachPerMillion;
    }

	/**
     * @param field_type $countryReachPerMillion
     */
    public function setCountryReachPerMillion($countryReachPerMillion)
    {
        $this->countryReachPerMillion = $countryReachPerMillion;
    }

	/**
     * @return the $countryPageViewsPerMillion
     */
    public function getCountryPageViewsPerMillion()
    {
        return $this->countryPageViewsPerMillion;
    }

	/**
     * @param field_type $countryPageViewsPerMillion
     */
    public function setCountryPageViewsPerMillion($countryPageViewsPerMillion)
    {
        $this->countryPageViewsPerMillion = $countryPageViewsPerMillion;
    }

	/**
     * @return the $countryPageViewsPerUser
     */
    public function getCountryPageViewsPerUser()
    {
        return $this->countryPageViewsPerUser;
    }

	/**
     * @param field_type $countryPageViewsPerUser
     */
    public function setCountryPageViewsPerUser($countryPageViewsPerUser)
    {
        $this->countryPageViewsPerUser = $countryPageViewsPerUser;
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
