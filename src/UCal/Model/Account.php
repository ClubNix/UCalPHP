<?php

namespace UCal\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ucal_users")
 * @property string $login
 * @property string $resourcesIds
 * 
 */
class Account
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="login",type="string");
	 */
	protected $login;

	/**
	 * @ORM\Column(name="resourcesIds",type="string");
	 */
	protected $resourcesIds;
	
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