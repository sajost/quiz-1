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
     * Text der Frage
     * 
     * @ORM\Column(type="string")
     */
    public $title;
    
    /**
     * 0 = aktiv, nicht geprüft; 1 = aktiv, geprüft; 2 = pausiert, nicht geprüft; 3 = pausiert, geprüft
     * 
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $status = 1;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $truecount = 1;
    
    /**
     * Tag, an dem die Frage in die Datenbank eingetragen wurde (wird automatisch befüllt)
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $published;

    /**
     * Quelle, die die Frage hervorgebracht hat (z. B. "Wikipedia")
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    public $source;
    
    /**
     * Schwierigkeitsgrad von 0 = sehr enfach bis 9 = schwer (für den User als 1 bis 10 dargestellt)
     * 
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
     * @ORM\OneToMany(targetEntity="QuizQuestion", mappedBy="question", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    public $quizquestions;
    

    /**
     * @ORM\Column(type="datetime")
     */
    public $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $updated;
    
    /**
     * temp value for count of answers in the form
     * @var integer
     */
    public $answercount = 4;
    
    /**
     * @var string
     */
    public $typ = 'question';
    
    public $quizin = 0;
    
    
    public function __construct()
    {
    	$this->answers = new ArrayCollection();
    	
    	$this->setCreated(new \DateTime());
    	$this->setUpdated(new \DateTime());
    	
    	$this->cats = new ArrayCollection();
    	$this->tags = new ArrayCollection();
    	
    	$this->quizs = new ArrayCollection();
    	
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
	public function getTruecount() {
		return $this->truecount;
	}
	public function setTruecount($truecount) {
		$this->truecount = $truecount;
		return $this;
	}
	public function getPublished() {
		return $this->published;
	}
	public function setPublished($published) {
		$this->published = $published;
		return $this;
	}
	public function getSource() {
		return $this->source;
	}
	public function setSource($source) {
		$this->source = $source;
		return $this;
	}
	public function getDifficulty() {
		return $this->difficulty;
	}
	public function setDifficulty($difficulty) {
		$this->difficulty = $difficulty;
		return $this;
	}
	public function getCats() {
		return $this->cats;
	}
	public function setCats($cats) {
		$this->cats = $cats;
		return $this;
	}
	public function getTags() {
		return $this->tags;
	}
	public function setTags($tags) {
		$this->tags = $tags;
		return $this;
	}
	public function getAnswers() {
		return $this->answers;
	}
	public function setAnswers($answers) {
		$this->answers = $answers;
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
	
	/**
	 * Add answers
	 *
	 * @param \AppBundle\Entity\CarBookmark $answers
	 * @return Car
	 */
	public function addAnswer(Answer $answer)
	{
		if (!$this->answers->contains($answer)) {
			$this->answers->add($answer);
			$answer->setParent($this);
		}
		return $this;
	}
	
	/**
	 * Remove answers
	 *
	 * @param \AppBundle\Entity\CarBookmark $answers
	 */
	public function removeAnswer(Answer $answer)
	{
		if ($this->answers->contains($answer)) {
			$this->answers->removeElement($answer);
			$answer->setParent(null);
		}
		return $this;
	}
	
	
	/**
	 * Add $quizquestion
	 *
	 * @param \AppBundle\Entity\QuizQuestion $quizquestion
	 * @return Question
	 */
	public function addQuizquestion(QuizQuestion $quizquestion)
	{
		if (!$this->quizs->contains($quizquestion)) {
			$this->quizs->add($quizquestion);
			//$quiz->setQuiz($this);
		}
		return $this;
	}
	
	/**
	 * Remove $quizquestion
	 *
	 * @param \AppBundle\Entity\QuizQuestion $quiz
	 */
	public function removeQuizquestion(QuizQuestion $quizquestion)
	{
		if ($this->quizs->contains($quizquestion)) {
			$this->quizs->removeElement($quizquestion);
		}
		return $this;
	}
	public function getQuizquestions() {
		return $this->quizquestions;
	}
	public function setQuizquestions($quizquestions) {
		$this->quizquestions = $quizquestions;
		return $this;
	}
	
	
}
