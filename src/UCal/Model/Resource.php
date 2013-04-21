<?php

namespace UCal\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="resources")
 * @property int $id
 * @property string $name
 * @property string $category
 * @property int $isGroup
 * @property string $type
 * @property string $email
 * @property int $fatherId
 * @property string $info
 * @property string $codeX
 * @property string $codeY
 * @property string $codeZ
 *
 */
class Resource
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="resourceId",type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="resourceName",type="string");
	 */
	protected $name;

	/**
	 * @ORM\Column(name="resourceCategory",type="string");
	 */
	protected $category;

	/**
	 * @ORM\Column(name="resourceIsGroup",type="integer");
	 */
	protected $isGroup;

	/**
	 * @ORM\Column(name="resourceType",type="string");
	 */
	protected $type;

	/**
	 * @ORM\Column(name="resourceEmail",type="string");
	 */
	protected $email;

	/**
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="children")
     * @ORM\JoinColumn(name="resourceFatherId", referencedColumnName="resourceId")
     */
	protected $father;
	
	/**
	 * @ORM\OneToMany(targetEntity="Resource", mappedBy="father")
	 */
	protected $children;

	/**
	 * @ORM\Column(name="resourceInfo",type="string");
	 */
	protected $info;	

	/**
	 * @ORM\Column(name="resourceCodeX",type="string");
	 */
	protected $codeX;

	/**
	 * @ORM\Column(name="resourceCodeY",type="string");
	 */
	protected $codeY;
	
	/**
	 * @ORM\Column(name="resourceCodeZ",type="string");
	 */
	protected $codeZ;
	
	public function __construct() {
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	/**
	 * Magic getter to expose protected properties.
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property)
	{
		return $this->$property;
	}

	/**
	 * Magic setter to save protected properties.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	public function __set($property, $value)
	{
		$this->$property = $value;
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
		$this->id = $data['id'];
	}

}