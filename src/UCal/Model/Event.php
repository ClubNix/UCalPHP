<?php

namespace UCal\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="events")
 * @property int $id
 * @property string $id
 */
class Event
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="eventId",type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="eventWeek",type="integer");
     */
    protected $week;

    /**
     * @ORM\Column(name="eventDay",type="integer");
     */
	protected $day;
    /**
     * @ORM\Column(name="eventSlot",type="integer");
     */
    protected $slot;

    /**
     * @ORM\Column(name="eventDuration",type="integer");
     */
    protected $duration;

    /**
     * @ORM\ManyToOne(targetEntity="Activity",fetch="EAGER")
     * @ORM\JoinColumn(name="eventActivityId", referencedColumnName="activityId")
     **/
    protected $activity;
    
    /**
     * @ORM\ManyToMany(targetEntity="Resource",fetch="EAGER")
     * @ORM\JoinTable(name="events_resources",
     *      joinColumns={@ORM\JoinColumn(name="eventId", referencedColumnName="eventId")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="resourceId", referencedColumnName="resourceId")}
     *      )
     **/
    private $resources;

    public function __construct() {
    	$this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property) 
    {
    	switch($property) {
    		case 'startDate':
    			$startDate = new \DateTime();
    			$start = $this->slot*15 + 7*60+30;
    			$startDate->setTime((int) ($start/60), $start%60, 0);
    			$startDate->setISODate(date('o'), $this->week - 19, $this->day+1);
    			return $startDate;
    			break;
    		case 'endDate':
    			$endDate = new \DateTime();
    			$end = $this->slot*15 + 7*60+30 + $this->duration*15;
    			$endDate->setTime((int) ($end/60), $end%60, 0);
    			$endDate->setISODate(date('o'), $this->week - 19, $this->day+1);
    			return $endDate;
    			break;
    		default:
		        return $this->$property;
    			break;
    	}
    	
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