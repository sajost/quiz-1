<?php
// src/AppBundle/Entity/Answer.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnswerRepository")
 * @ORM\Table(name="answer")
 * @ORM\HasLifecycleCallbacks
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO") 
     */
    public $id;

    /**
     * @ORM\Column(type="text")
     */
    public $title;
    
    /**
     * 0 = falsch; 1 = richtig;
     *
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $status = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    public $parent;
    

    /**
     * @ORM\Column(type="datetime")
     */
    public $created;

    /**
     * @ORM\Column(type="datetime")
     */
    public $updated;
    
    
    public $typ = 'answer';
    

    public function __construct()
    {
    	$this->answers = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
       $this->setUpdated(new \DateTime());
    }

	    
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
    	
    }
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
	
	
	
	
	public function getCreated() {
		return $this->created;
	}
	public function setCreated($created) {
		$this->created = $created;
		return $this;
	}
	public function getUpdated() {
		return $this->updated;
	}
	public function setUpdated($updated) {
		$this->updated = $updated;
		return $this;
	}
	
	/* This is the static comparing function: */
	public static function sort_created($a, $b)
	{
		$al = $a->getCreated();
		$bl = $b->getCreated();
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	
	public function __toString()
	{
		return $this->getTitle();
	}
	
	public function getTyp() {
		return $this->typ;
	}
	
	
	public function getTitle() {
		return $this->title;
	}
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	public function getParent() {
		return $this->parent;
	}
	public function setParent($parent) {
		$this->parent = $parent;
		return $this;
	}
	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}
	
	
	
	
	
}
