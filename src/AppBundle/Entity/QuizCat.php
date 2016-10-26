<?php
// src/AppBundle/Entity/QuizCat.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuizCatRepository")
 * @ORM\Table(name="quizcat")
 * @ORM\HasLifecycleCallbacks
 */
class QuizCat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    
    /**
     * @ORM\Column(type="string", length=20)
     */
    public $title;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $status = 1;
    
    
    /**
     * @ORM\OneToOne(targetEntity="QuestionCat", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="questioncat", referencedColumnName="id")
     */
    public $questioncat;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="Quiz", mappedBy="cats")
     */
    public $quizs;
    
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $updated;
    
    public $typ = 'quizcat';
    
    
    public function __construct()
    {
    	$this->id=0;
    	
    	$this->quizs = new \Doctrine\Common\Collections\ArrayCollection();
    	
    	$this->setCreated(new \DateTime());
    	$this->setUpdated(new \DateTime());
    }
    
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
    	$this->setUpdated(new \DateTime());
    }
    
    public function addQuiz(Quiz $quiz)
    {
    	$this->quiz[] = $quiz;
    }
    
    
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getTitle() {
		return $this->title;
	}
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
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
	public function getTyp() {
		return $this->typ;
	}
	public function getQuizs() {
		return $this->quizs;
	}
	public function setQuizs($quizs) {
		$this->quizs = $quizs;
		return $this;
	}
	
	
	public function __toString(){
		return $this->title;
	}
	public function getQuestioncat() {
		return $this->questioncat;
	}
	public function setQuestioncat($questioncat) {
		$this->questioncat = $questioncat;
		return $this;
	}
	

    
}
