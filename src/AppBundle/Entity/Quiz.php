<?php
// src/AppBundle/Entity/Quiz.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuizRepository")
 * @ORM\Table(name="quiz")
 * @ORM\HasLifecycleCallbacks
 */
class Quiz
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
     * @ORM\Column(type="string", length=20)
     */
    public $title;
    
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $loginrequired = 0;
    
    /**
     * legt fest, wie viele Runden pro Tag gespielt werden können: 0 = unbegrenzt; 1 = 1 Runde etc.
     * 
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $roundperday = 0;
    
    /** wie viele Platzierungen auf der Highscore-Liste zu sehen sind.
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $hsnumber = 0;
    
    /**legt fest, ob nach dem "Game over" eine Auswertung des Ergebnisses erfolgt: 0 = nein; 1 = ja
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $analysis = 1;
    
    /**legt fest, ob der Spieler für sein Ergebnis eine Belohnung (Medaille, Trophäe etc.) erhält
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $reward = 1;
    
    
    /**legt fest, ob ein Highscore geteilt werden kann: 0 = nein; 1 = ja
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $sharehs = 1;
    
    /**legt fest, ob die Auswertung geteilt werden kann: 0 = nein; 1 = ja
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $shareanalysis = 1;
    
    /**legt fest, ob eine Belohnung geteilt werden kann: 0 = nein; 1 = ja
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $sharereward = 1;
    
    
    /**legt fest, ob Joker-Typ "50:50" für das Quiz aktiviert ist
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $joker5050 = 1;
    
    /**legt fest, ob Joker-Typ "Zeit stoppen" für das Quiz aktiviert ist
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $jokerpause = 1;
    
    
    /**legt fest, ob Joker-Typ "Frage überspringen" für das Quiz aktiviert ist
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $jokerskip = 1;
    
    /**legt fest, ob Fragen der Schwierigkeitsstufe 0 - 9
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    public $difficulty = 1;
    
    /**legt fest, wie viele Sekunden das Zeitlimit andauert 0-kein, 1-n Sekunden
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $timelimit = 0;
    
    /**legt fest, ob die Reihenfolge der gestellten Fragen zufällig ist: 0 = nach Erstelldatum; 1 = Random
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $random = 0;
    
    /**legt fest, ob bereits gestellte Fragen in einer Spielrunde wiederholt werden können
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $repeat = 0;
    
    /**legt fest, ob Fragen des Frage-Typs "0-Alle", "1-Entscheidungsfragen", "2-Multiple-Choice mit mehreren richtigen Antworten" im Quiz genutzt werden.
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $trueanswer = 0;
    
    /**
     * @ORM\ManyToMany(targetEntity="QuizCat", inversedBy="quizs")
     * @ORM\JoinTable(name="quizs_cats")
     */
    public $cats;
    
    
    /**
     * @ORM\OneToMany(targetEntity="QuizQuestion", mappedBy="quiz", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
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
    
    public $typ = 'quiz';
    
    public function __construct()
    {
    	$this->cats = new ArrayCollection();
    	$this->quizquestions = new ArrayCollection();
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
    
    public function addCat(QuizCat $cat)
    {
    	$cat->addQuiz($this); // synchronously updating inverse side
    	$this->cats[] = $cat;
    }
    
    
    /**
     * Add quizquestions
     *
     * @param \AppBundle\Entity\QuizQuestion $quizquestion
     * @return Car
     */
    public function addQuizquestion(QuizQuestion $quizquestion)
    {
    	if (!$this->quizquestions->contains($quizquestion)) {
    		$this->quizquestions->add($quizquestion);
    	}
    	return $this;
    }
    
    /**
     * Remove quizquestions
     *
     * @param \AppBundle\Entity\QuizQuestion $quizquestion
     */
    public function removeQuizquestion(QuizQuestion $quizquestion)
    {
    	if ($this->quizquestions->contains($quizquestion)) {
    		$this->quizquestions->removeElement($quizquestion);
    	}
    	return $this;
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
	public function getTitle() {
		return $this->title;
	}
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	public function getLoginrequired() {
		return $this->loginrequired;
	}
	public function setLoginrequired($loginrequired) {
		$this->loginrequired = $loginrequired;
		return $this;
	}
	public function getRoundperday() {
		return $this->roundperday;
	}
	public function setRoundperday($roundperday) {
		$this->roundperday = $roundperday;
		return $this;
	}
	public function getHsnumber() {
		return $this->hsnumber;
	}
	public function setHsnumber($hsnumber) {
		$this->hsnumber = $hsnumber;
		return $this;
	}
	public function getAnalysis() {
		return $this->analysis;
	}
	public function setAnalysis($analysis) {
		$this->analysis = $analysis;
		return $this;
	}
	public function getReward() {
		return $this->reward;
	}
	public function setReward($reward) {
		$this->reward = $reward;
		return $this;
	}
	public function getSharehs() {
		return $this->sharehs;
	}
	public function setSharehs($sharehs) {
		$this->sharehs = $sharehs;
		return $this;
	}
	public function getShareanalysis() {
		return $this->shareanalysis;
	}
	public function setShareanalysis($shareanalysis) {
		$this->shareanalysis = $shareanalysis;
		return $this;
	}
	public function getSharereward() {
		return $this->sharereward;
	}
	public function setSharereward($sharereward) {
		$this->sharereward = $sharereward;
		return $this;
	}
	public function getJoker5050() {
		return $this->joker5050;
	}
	public function setJoker5050($joker5050) {
		$this->joker5050 = $joker5050;
		return $this;
	}
	public function getJokerpause() {
		return $this->jokerpause;
	}
	public function setJokerpause($jokerpause) {
		$this->jokerpause = $jokerpause;
		return $this;
	}
	public function getJokerskip() {
		return $this->jokerskip;
	}
	public function setJokerskip($jokerskip) {
		$this->jokerskip = $jokerskip;
		return $this;
	}
	public function getDifficulty() {
		return $this->difficulty;
	}
	public function setDifficulty($difficulty) {
		$this->difficulty = $difficulty;
		return $this;
	}
	public function getTimelimit() {
		return $this->timelimit;
	}
	public function setTimelimit($timelimit) {
		$this->timelimit = $timelimit;
		return $this;
	}
	public function getRandom() {
		return $this->random;
	}
	public function setRandom($random) {
		$this->random = $random;
		return $this;
	}
	public function getRepeat() {
		return $this->repeat;
	}
	public function setRepeat($repeat) {
		$this->repeat = $repeat;
		return $this;
	}
	public function getTrueanswer() {
		return $this->trueanswer;
	}
	public function setTrueanswer($trueanswer) {
		$this->trueanswer = $trueanswer;
		return $this;
	}
	public function getQuizquestions() {
		return $this->quizquestions;
	}
	public function setQuizquestions($quizquestions) {
		$this->quizquestions = $quizquestions;
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
	public function getCats() {
		return $this->cats;
	}
	public function setCats($cats) {
		$this->cats = $cats;
		return $this;
	}
	
	

    

}
