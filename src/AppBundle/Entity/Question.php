<?php
// src/AppBundle/Entity/Question.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
 * @ORM\Table(name="question")
 * @ORM\HasLifecycleCallbacks
 */
class Question 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    public $user;
    
    /**
     * @ORM\Column(type="string")
     */
    public $title;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $status = 1;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $truecount = 1;
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $published;

    /**
     * @ORM\Column(type="text")
     */
    public $source;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $difficulty = 0;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="QuestionCat", inversedBy="questions")
     * @ORM\JoinTable(name="questions_cats")
     */
    public $cats;
    
    /**
     * @ORM\ManyToMany(targetEntity="QuestionTag", inversedBy="questions")
     * @ORM\JoinTable(name="questions_tags")
     */
    public $tags;
    

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="parent", cascade={"persist", "remove"})
     */
    public $answers;
    

    /**
     * @ORM\Column(type="datetime")
     */
    public $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $updated;
    
    
    /**
     * @var string
     */
    public $typ = 'question';
    
    
    public function __construct()
    {
    	$this->answers = new ArrayCollection();
    	
    	$this->setCreated(new \DateTime());
    	$this->setUpdated(new \DateTime());
    	
    	$this->cats = new ArrayCollection();
    	$this->tags = new ArrayCollection();
    	
    }
    
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
    	$this->setUpdated(new \DateTime());
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
	
    
    public function __toString()
    {
    	return $this->getTitle();
    }
    
	public function getTyp() {
		return $this->typ;
	}
	
	public function addCat(QuestionCat $cat)
	{
		$cat->addQuestion($this); // synchronously updating inverse side
		$this->cats[] = $cat;
	}
	
	public function addTag(QuestionTag $tag)
	{
		$tag->addQuestion($this); // synchronously updating inverse side
		$this->tags[] = $tag;
	}
	
	
}
