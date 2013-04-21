<?php

namespace UCal\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="activities")
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $folderId
 * @property int $size
 * @property int $repetition
 * 
 */
class Activity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="activityId",type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="activityName",type="string");
	 */
	protected $name;

	/**
	 * @ORM\Column(name="activityType",type="string");
	 */
	protected $type;


	/**
	 * @ORM\Column(name="activityFolderId",type="integer");
	 */
	protected $folderId;
	

	/**
	 * @ORM\Column(name="activitySize",type="integer");
	 */
	protected $size;
	

	/**
	 * @ORM\Column(name="activityRepetition",type="integer");
	 */
	protected $repetition;
	
	
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