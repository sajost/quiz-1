<?php
// src/AppBundle/Entity/Quiz.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Quiz")
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
     * @ORM\OneToMany(targetEntity="QuizQuestion", mappedBy="quiz", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    public $questions;
    

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
    	$this->questions = new ArrayCollection();
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
    
    
    /**
     * Add questions
     *
     * @param \AppBundle\Entity\CarBookmark $questions
     * @return Car
     */
    public function addQuestion(QuizQuestion $question)
    {
    	if (!$this->questions->contains($question)) {
    		$this->questions->add($question);
    		$question->setQuiz($this);
    	}
    	return $this;
    }
    
    /**
     * Remove questions
     *
     * @param \AppBundle\Entity\CarBookmark $questions
     */
    public function removeQuestion(QuizQuestion $question)
    {
    	if ($this->questions->contains($question)) {
    		$this->questions->removeElement($question);
    		$question->setQuiz(null);
    	}
    	return $this;
    }

    

}
