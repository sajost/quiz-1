<?php
// src/AppBundle/Entity/QuizQuestion.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuizQuestionRepository")
 * @ORM\Table(name="quiz_question")
 * @ORM\HasLifecycleCallbacks
 */
class QuizQuestion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    
	
	/**
	 * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="questions")
	 * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id", nullable=FALSE)
	 */
	public $quiz;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Car")
	 * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=FALSE)
	 */
	public $question;
	
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	public $info;
    
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $updated;
    
    
    
    public function __construct(Quiz $quiz=null, Question $question=null)
    {
    	$this->setCreated(new \DateTime());
    	$this->setUpdated(new \DateTime());
    	$this->quiz=$quiz;
    	$this->question=$question;
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
	
    
        
}
